describe('Admin: filtro y búsqueda pública de productos', () => {
  const timestamp = Date.now();
  const productName = `FILT-${timestamp}`;

  before(() => {
    cy.loginSession(); // sesión cacheada desde support/commands.js
  });

  it('Crea producto y lo encuentra usando el filtro admin y el buscador público', () => {
    cy.adminCreateProduct({ name: productName, price: 1.5, stock: 5 }).then((prod) => {
      expect(prod).to.have.property('name');

      // Verificar filtro en admin
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(prod.name)}`);
      cy.contains(new RegExp(prod.name)).should('exist');

      // Verificar búsqueda pública (si existe input[name="q"])
      cy.visit('/');
      cy.get('body').then($b => {
        if ($b.find('input[name="q"]').length) {
          cy.get('input[name="q"]').clear().type(`${prod.name}{enter}`);
        } else if ($b.find('input[name="s"]').length) {
          cy.get('input[name="s"]').clear().type(`${prod.name}{enter}`);
        } else {
          cy.visit(`/?q=${encodeURIComponent(prod.name)}`);
        }
      });
      cy.contains(new RegExp(prod.name)).should('be.visible');
    });
  });
});