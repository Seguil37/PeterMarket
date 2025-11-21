// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

// Login reutilizable (usa data-cy)
// Login robusto
// --- ya existente ---
// cypress/support/commands.js

// Login “normal” por UI (lo usamos dentro de cy.session)
Cypress.Commands.add('login', (email = 'admin@tuapp.com', password = 'clave-super-segura') => {
  cy.visit('/admin/login');
  cy.get('input[name="email"], #email, [data-cy="login-email"]').clear().type(email);
  cy.get('input[name="password"], #password, [data-cy="login-password"]').clear().type(password);

  cy.get('body').then(($b) => {
    const btn = $b.find('button[type="submit"], [data-cy="login-submit"], input[type="submit"], button:contains("Entrar al panel")');
    if (btn.length) {
      cy.wrap(btn[0]).click({ force: true });
    } else {
      const form = $b.find('form[action*="login"], form[action="/login"], form');
      if (form.length) {
        form[0].requestSubmit ? form[0].requestSubmit() : form[0].submit();
      } else {
        cy.get('input[name="password"]').type('{enter}');
      }
    }
  });

  cy.url({ timeout: 10000 }).should('include', '/admin'); // tu dashboard
});

// ✅ Nueva: sesión cacheada entre tests (Cypress 12+)
Cypress.Commands.add('loginSession', (email = 'admin@tuapp.com', password = 'clave-super-segura') => {
  cy.session([email, password], () => {
    cy.login(email, password);
  }, {
    cacheAcrossSpecs: true, // mantiene la sesión entre specs si lo deseas
  });
});

// Visitar como admin (si te manda al login, recupera sesión y reintenta)
Cypress.Commands.add('visitAsAdmin', (path) => {
  // Asegura la sesión de administrador antes de visitar la ruta protegida
  cy.loginSession();

  cy.visit(path, { failOnStatusCode: false });
  cy.url().then((u) => {
    if (u.includes('/login')) {
      cy.loginSession();
      cy.visit(path, { failOnStatusCode: false });
    }
  });
});

// Utilidad para extraer ID desde link Editar en índice de productos
function extractProductIdFromContext(contextSel = 'body') {
  return cy.get(contextSel).then($ctx => {
    const $link = $ctx.find('a[href*="/admin/products/"][href*="/edit"]').first();
    if ($link.length) {
      const href = $link.attr('href') || '';
      const m = href.match(/\/admin\/products\/(\d+)\/edit/);
      if (m && m[1]) return Number(m[1]);
    }
    return null;
  });
}

// Crear producto en Admin y devolver { id, name, ... } robusto
Cypress.Commands.add('adminCreateProduct', (p = {}) => {
  const prod = {
    name: p.name || `CYP-${Date.now()}`,
    description: p.description || 'Producto creado por Cypress',
    price: p.price ?? 9.99,
    stock: p.stock ?? 25,
    image_url: p.image_url || 'https://via.placeholder.com/200',
    category_type: p.category_type || 'Lácteos',
  };

  cy.visitAsAdmin('/admin/products');
  cy.contains(/nuevo|crear/i).click({ force: true });

  cy.get('input[name="name"]').clear().type(prod.name);
  cy.get('select[name="category_type"]').select(prod.category_type);
  cy.get('textarea[name="description"]').clear().type(prod.description);
  cy.get('input[name="price"]').clear().type(String(prod.price));
  cy.get('input[name="stock"]').clear().type(String(prod.stock));
  cy.get('input[name="image_url"]').clear().type(prod.image_url);
  cy.contains(/guardar|crear|guardar cambios|cargar imagen/i).click({ force: true });

  cy.url().should('include', '/admin/products');

  // Filtra por nombre y extrae ID
  cy.visit(`/admin/products?s=${encodeURIComponent(prod.name)}`);
  cy.contains(new RegExp(prod.name))
    .closest('tr, .row, .card, li, div')
    .then($row => {
      if ($row && $row.length) {
        cy.wrap($row).then(() => extractProductIdFromContext($row)).then(id => {
          if (id) return cy.wrap({ ...prod, id });
          return extractProductIdFromContext('body').then(id2 => cy.wrap({ ...prod, id: id2 || null }));
        });
      } else {
        return extractProductIdFromContext('body').then(id2 => cy.wrap({ ...prod, id: id2 || null }));
      }
    });
});

// ✅ POST /cart sin CSRF (con bypass local/testing vía X-CYPRESS)
Cypress.Commands.add('addToCartById', (productId, quantity = 1) => {
  cy.visit('/'); // asegura cookies de sesión

  cy.request({
    method: 'POST',
    url: '/cart',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CYPRESS': '1',           // <-- requiere override en VerifyCsrfToken (local/testing)
      'Accept': 'application/json',
    },
    body: { product_id: productId, quantity },
  }).then((res) => {
    expect([200, 302]).to.include(res.status);
  });
});

// Fallback UI por nombre (por si no obtienes el ID)
Cypress.Commands.add('addToCartByNameUI', (productName, quantity = 1) => {
  cy.visit('/');
  cy.contains(new RegExp(productName))
    .closest('.product-card, .card, li, .item, div')
    .within(() => {
      if (quantity > 1) cy.get('input[name="quantity"]').clear().type(String(quantity));
      cy.contains(/agregar|añadir|add to cart|agregar al carrito/i).click({ force: true });
    });
});

