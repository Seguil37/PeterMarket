  describe('Flujo simple: crear producto, agregar al carrito por UI y verificar subtotal', () => {
    const qty = 2;
    const price = 5.5;
    const name = `E2E-CART-${Date.now()}`;

    before(() => {
      cy.loginSession(); // usa la sesión cacheada en support/commands.js
    });

    it('Crea producto en Admin, lo añade al carrito por UI y verifica subtotal', () => {
      cy.adminCreateProduct({ name, price, stock: 10 }).then((prod) => {
        expect(prod).to.have.property('name');

        // Ir al catálogo público y buscar el producto
        cy.visit('/');
        cy.get('body').then($b => {
          if ($b.find('input[name="q"], input[name="s"], #q').length) {
            cy.get('input[name="q"], input[name="s"], #q').first().clear().type(`${prod.name}{enter}`);
          } else {
            // fallback: buscar por texto en la página
            cy.contains(new RegExp(prod.name)).scrollIntoView();
          }
        });

        // Localizar tarjeta/fila del producto y agregar cantidad
        cy.contains(new RegExp(prod.name)).closest('.product-card, .card, li, .item, tr, div').within(() => {
          // si hay input de cantidad, setearla
          cy.get('input[type="number"], input[name="quantity"], input.qty', { timeout: 3000 }).then($inq => {
            if ($inq && $inq.length) {
              cy.wrap($inq.first()).clear().type(String(qty));
            }
          });
          // hacer click en botón de agregar con texto tolerante
          cy.contains(/agregar|añadir|add to cart|agregar al carrito/i, { timeout: 5000 }).click({ force: true });
        });

        // Ir al carrito y verificar producto y subtotal
        cy.visit('/cart');
        cy.contains(new RegExp(prod.name)).should('exist');

        // Verificar cantidad en la fila del producto
        cy.contains(new RegExp(prod.name)).closest('tr, .cart-item, .row, div').within(() => {
          cy.get('input[type="number"], input[name="quantity"], .qty', { timeout: 3000 }).then($i => {
            if ($i && $i.length) {
              cy.wrap($i.first()).invoke('val').should('eq', String(qty));
            } else {
              // fallback: esperar que aparezca el número de cantidad en texto
              cy.contains(new RegExp(String(qty))).should('exist');
            }
          });
        });

        // Verificar subtotal (precio * qty). Formato tolerante (S/ or $ or plain number)
        const expectedSubtotal = (Number(price) * Number(qty)).toFixed(2);
        cy.contains(new RegExp(expectedSubtotal)).should('exist');
      });
    });
  });