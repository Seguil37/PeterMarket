describe('Compra completa como cliente – Peter Market', () => {

  const baseUrl = 'http://127.0.0.1:8000';

  const userEmail = 'usuario1347@test.com';
  const userPassword = '12345678';

  const nombreProducto = 'Papel higiénico x12';

  const checkout = {
    nombre: 'Seguil S.O',
    correo: 'seguilso37@gmail.com',
    direccion: 'Apv san Antonio g-8-2',
    ciudad: 'Cusco',
    referencia: 'casa color lila',
  };

  it('Realiza una compra completa', () => {

    // 1. Ir al login
    cy.visit(`${baseUrl}/login`);

    // 2. Loguearse
    cy.get('input[name="email"]').type(userEmail);
    cy.get('input[name="password"]').type(userPassword);

    cy.contains('button', 'Entrar').click();

    // 3. Ir a Productos
    cy.contains('a', 'Productos').click();

    // 4. Buscar producto y entrar a Ver más
    cy.contains('article', nombreProducto)
      .within(() => cy.contains('a', 'Ver más del producto').click());

    // 5. Verificar página del producto
    cy.url().should('include', '/products/');
    cy.contains(nombreProducto).should('exist');

    // 6. Seleccionar cantidad
    cy.get('input[name="quantity"]').clear().type('5');

    // 7. Añadir al carrito
    cy.contains('button', 'Añadir al carrito').click();

    // 8. Ir al carrito
    cy.contains('a', 'Carrito').click();

    // 9. AVISO IMPORTANTE:
    // En tu proyecto NO existe el botón "Comprar", así que pasamos directo al checkout
    cy.url().should('include', '/cart');

    // 10. Llenar formulario de checkout
    cy.get('input[name="customer_name"]').clear().type(checkout.nombre);
    cy.get('input[name="customer_email"]').clear().type(checkout.correo);

    cy.get('input[name="shipping_address"]').clear().type(checkout.direccion);
    cy.get('input[name="shipping_city"]').clear().type(checkout.ciudad);
    cy.get('input[name="shipping_reference"]').clear().type(checkout.referencia);

    // Asegurar pago simulado
    cy.contains('label', 'Pago simulado').click({ force: true });

    // 11. Pagar ahora
    cy.contains('button', 'Pagar ahora').click();

    // 12. Verificación final
    cy.contains('Gracias', { timeout: 10000 }).should('exist'); // Ajusta al mensaje real
  });

});
