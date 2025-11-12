describe('Admin: crear producto y verificar en admin + catálogo', () => {
  const name = `CYP-E2E-CREATE-${Date.now()}`;

  before(() => {
    cy.loginSession(); // usa la sesión cacheada definida en support/commands.js
  });

  it('Crea producto en Admin y aparece en Admin y Catálogo público', () => {
    cy.adminCreateProduct({ name, price: 3.5, stock: 8 }).then((prod) => {
      expect(prod).to.have.property('name', prod.name);

      // Verificar en admin (lista)
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(prod.name)}`);
      cy.contains(new RegExp(prod.name)).should('exist');

      // Verificar en catálogo público (welcome)
      cy.visit('/');
      cy.get('body').then($b => {
        if ($b.find('input[name="q"], #q').length) {
          cy.get('input[name="q"], #q').first().clear().type(`${prod.name}{enter}`);
        } else {
          cy.visit(`/?q=${encodeURIComponent(prod.name)}`);
        }
      });
      cy.contains(new RegExp(prod.name)).should('be.visible');
    });
  });
});