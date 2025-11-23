describe('Registro y login de usuario normal - Peter Market', () => {

  const baseUrl = 'http://127.0.0.1:8000';
  const userName = 'Usuario Cypress';
  const userEmail = 'usuario1347@test.com';
  const userPassword = '12345678';

  it('Crea un usuario, cierra sesión y vuelve a iniciar sesión correctamente', () => {
    
    // 1. Ir al registro
    cy.visit(`${baseUrl}/register`);

    // 2. Rellenar formulario de registro
    cy.get('input[name="name"]').type(userName);
    cy.get('input[name="email"]').type(userEmail);
    cy.get('input[name="password"]').type(userPassword);
    cy.get('input[name="password_confirmation"]').type(userPassword);

    cy.contains('button', 'Registrar').click();

    // 3. Verificar que entró como usuario
    cy.url().should('not.include', '/register');
    cy.contains(userName).should('exist');

    // 4. Cerrar sesión
    cy.contains('button, a', 'Salir').click();

    // 5. Debe volver al HOME
    cy.url().should('eq', `${baseUrl}/`);

    // 6. Ir al login desde el navbar (como tú quieres)
    cy.contains('nav a', 'Entrar').click();

    // 7. Por si redirige mal, forzamos estar en /login
    cy.visit(`${baseUrl}/login`);

    // 8. Página de login cargada
    cy.contains('Iniciar sesión').should('exist');

    // 9. Login con el usuario creado
    cy.get('input[name="email"]').clear().type(userEmail);
    cy.get('input[name="password"]').clear().type(userPassword);

    // Botón exacto "Entrar"
    cy.contains('button', /^Entrar$/).click();

  });

});
