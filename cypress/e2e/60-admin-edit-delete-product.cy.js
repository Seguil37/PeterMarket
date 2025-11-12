describe('Admin: editar y eliminar producto (flujo simple)', () => {
  const timestamp = Date.now();
  const originalName = `EDIT-${timestamp}`;
  const updatedName = `EDIT-UPDATED-${timestamp}`;

  before(() => {
    cy.loginSession();
  });

  it('Crea, edita el nombre y elimina el producto desde Admin', () => {
    cy.adminCreateProduct({ name: originalName, price: 2.5, stock: 4 }).then((prod) => {
      // Abrir lista filtrada y editar
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(prod.name)}`);
      cy.contains(new RegExp(prod.name)).closest('tr, .row, .card, li, div').within(() => {
        cy.contains(/editar|modificar|edit/i).first().click({ force: true });
      });

      // Si no navegó, visitar la ruta de edición con id
      cy.url({ timeout: 5000 }).then(url => {
        if (!/\/edit/.test(url) && prod && prod.id) {
          cy.visitAsAdmin(`/admin/products/${prod.id}/edit`);
        }
      });

      // Actualizar nombre (selectores tolerantes)
      cy.get('input[name="name"], input#name, [data-cy="product-name"], textarea[name="name"], input[name="title"]', { timeout: 10000 })
        .first()
        .should('be.visible')
        .clear()
        .type(updatedName, { force: true });

      cy.contains(/guardar|actualizar|update|save/i, { timeout: 5000 }).first().click({ force: true });

      // Verificar nuevo nombre en la lista
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(updatedName)}`);
      cy.contains(new RegExp(updatedName)).should('exist');

      // -------------------------
      // ELIMINACIÓN FIABLE TOMANDO _token DEL FORM EN LA PÁGINA
      // -------------------------
      const nameToDelete = updatedName; // asegure variable en scope

      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(nameToDelete)}`);

      cy.contains(new RegExp(nameToDelete)).closest('tr, .row, .card, li, div').within(() => {
        cy.get('form[action*="/admin/products/"]').first().then($form => {
          const action = $form.attr('action') || (prod && prod.id ? `/admin/products/${prod.id}` : null);
          // intentar obtener token desde el propio form o meta csrf
          const tokenFromForm = $form.find('input[name="_token"]').val();
          const tokenFromMeta = Cypress.$('meta[name="csrf-token"]').attr('content');
          const token = tokenFromForm || tokenFromMeta || undefined;

          // si no tenemos action, fallback a usar prod.id
          const url = action || (prod && prod.id ? `/admin/products/${prod.id}` : null);
          if (!url) {
            // última opción: intentar click UI delete con confirm stub
            cy.window().then(win => cy.stub(win, 'confirm').returns(true));
            cy.contains(/eliminar|delete|remover/i).first().click({ force: true });
            return;
          }

          // enviar la petición simulando el submit del formulario (POST + _method=DELETE)
          cy.request({
            method: 'POST',
            url,
            form: true,
            body: token ? { _method: 'DELETE', _token: token } : { _method: 'DELETE' },
            failOnStatusCode: false,
          }).then((res) => {
            expect([200, 302, 204]).to.include(res.status);
          });
        });
      });

      // esperar y verificar que ya no aparece
      cy.wait(700);
      cy.visitAsAdmin(`/admin/products?s=${encodeURIComponent(nameToDelete)}`);
      cy.contains(new RegExp(nameToDelete)).should('not.exist');
    });
  });
});