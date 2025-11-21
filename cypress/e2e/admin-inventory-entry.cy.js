describe('Inventario: registrar entrada y comprobar stock', () => {
  const baseName = `CYP-E2E-INV-${Date.now()}`;
  const addQty = 5;

  before(() => {
    cy.loginSession();
  });

  it('Registra entrada en inventario y el stock se incrementa', () => {
    cy.adminCreateProduct({ name: baseName, price: 2.2, stock: 2 }).then((prod) => {
      // leer stock antes desde admin/products (columna stock es td index 3)
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(prod.name)}`);
      cy.contains(new RegExp(prod.name)).closest('tr').find('td').eq(4).invoke('text')
        .then(txt => {
          const before = Number(txt.trim()) || prod.stock || 0;

          // crear movimiento en inventario
          cy.visitAsAdmin('/admin/inventario');
          cy.get('select[name="product_id"]').select(String(prod.id));
          cy.get('select[name="type"]').select('in');
          cy.get('input[name="quantity"]').clear().type(String(addQty));
          cy.get('input[name="unit_cost"]').clear().type('1.00');
          const note = `e2e-inv-${Date.now()}`;
          cy.get('input[name="note"]').clear().type(note);
          cy.contains(/guardar|registrar|submit/i).click({ force: true });

          // verificar incremento
          cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(prod.name)}`);
          cy.contains(new RegExp(prod.name)).closest('tr').find('td').eq(4).invoke('text')
            .then(txt2 => {
              const after = Number(txt2.trim()) || 0;
              expect(after).to.eq(before + addQty);
            });

          // verificar que el movimiento aparece (por nota)
          cy.visitAsAdmin('/admin/inventario');
          cy.contains(note).should('exist');
        });
    });
  });
});