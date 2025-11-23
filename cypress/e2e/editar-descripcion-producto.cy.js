


describe('Editar producto y verificar descripción en vista pública', () => {

  const baseUrl = 'http://127.0.0.1:8000';
  const email = 'admin@tuapp.com';
  const password = 'clave-super-segura';
  const nombreProducto = 'Papel higiénico x12';
  const nuevaDescripcion = 'PRUEBA CYPRESS';

  it('Cambia la descripción en admin y la ve en la página del producto', () => {

    // 1. Login en /admin/login
    cy.visit(`${baseUrl}/admin/login`);

    cy.get('input[name="email"]').type(email);
    cy.get('input[name="password"]').type(password);
    cy.contains('button', 'Entrar').click();

    // 2. Ya estamos en el Panel de Admin
    cy.contains('CRUD productos.').should('exist');

    // 3. Ir a CRUD Productos
    cy.visit(`${baseUrl}/admin/products`);

    // 4. Buscar el producto por nombre y darle CLICK a Editar
    cy.contains('tr', nombreProducto)
    .within(() => {
      cy.contains('a', 'Editar').click();
    });

    // 5. Cambiar descripción
    cy.get('textarea[name="description"]')
      .clear()
      .type(nuevaDescripcion);

    // 6. Click en CARGAR IMAGEN (submit)
    cy.get('#btnCargarImagen').click();

    // 7. Confirmar mensaje de éxito
    cy.contains('Producto actualizado.').should('be.visible');

    // 8. Ir a la página PÚBLICA de productos
    cy.visit(baseUrl);

    cy.contains('a', 'Productos').click();

    // 9. Buscar la tarjeta (article) del producto y entrar a "Ver más"
    cy.contains('article', nombreProducto)   // article que incluye "Gaseosa cola 2.25L"
      .within(() => {
        cy.contains('a', 'Ver más').click();
      });

    // 10. Verificar que la descripción aparezca en la página individual
    cy.url().should('include', '/products/');
    cy.contains(nuevaDescripcion).should('be.visible');

  });

});
