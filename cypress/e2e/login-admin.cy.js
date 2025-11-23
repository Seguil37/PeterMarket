// cypress/e2e/login-admin.cy.js

describe('Login administrador y navegación del panel - Peter Market', () => {

  const baseUrl = 'http://127.0.0.1:8000';
  const email = 'admin@tuapp.com';
  const password = 'clave-super-segura';

  it('Debe iniciar sesión y navegar por todos los módulos del panel admin', () => {

    // 1) LOGIN
    cy.visit(`${baseUrl}/admin/login`);

    cy.get('input[name="email"]').type(email);
    cy.get('input[name="password"]').type(password);

    cy.contains('button', 'Entrar al panel').click();

    // Validación inicial del panel
    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');


    // ============================================================
    // ===============   NAVEGACIÓN UNO POR UNO  ==================
    // ============================================================

    // ---- 1) Dashboard
    cy.contains('nav a', 'Dashboard').click();
    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');


    // ---- 2) Inventario
    cy.contains('nav a', 'Inventario').click();
    cy.url().should('include', '/admin/inventario');
    cy.contains('Inventario').should('exist');


    // 3. Ir a CRUD Productos
    cy.contains('nav a', 'Admin').click();
    cy.contains('CRUD productos.').should('exist');
    cy.visit(`${baseUrl}/admin/products`);


    // ---- 4) Administradores
    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');
    cy.contains('Administradores').should('exist');


    // Volver al Dashboard para terminar la prueba en orden
    cy.contains('nav a', 'Dashboard').click();
    cy.url().should('include', '/admin');
  });

});
