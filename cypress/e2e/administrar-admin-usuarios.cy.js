describe('Flujo completo de administración de usuarios', () => {
  const baseUrl = 'http://127.0.0.1:8000';

  const masterEmail = 'admin@tuapp.com';
  const masterPassword = 'clave-super-segura';

  const testAdminName = 'Manuel';
  const testAdminEmail = 'adminprueba@gmail.com';
  const testAdminPassword = '12345678'; // la que tú indicaste

  // Helper para iniciar sesión
  const loginAs = (email, password) => {
    cy.visit(`${baseUrl}/admin/login`);

    cy.get('input[name="email"]').clear().type(email);
    cy.get('input[name="password"]').clear().type(password);

    cy.contains('button', 'Entrar al panel').click();

    // Confirmamos que entró al panel
    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');
  };

  // Helper para salir (botón <button> Salir)
  const logout = () => {
    cy.get('header').contains('button', 'Salir').click();
    // Debería volver al login
    cy.contains('button', 'Entrar al panel').should('exist');
  };

  // 1) Crear admin Manuel como Admin Master
  it('Crea un nuevo admin Manuel como Admin Master', () => {
    loginAs(masterEmail, masterPassword);

    // Ir al módulo Administradores
    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');
    cy.contains('Administradores').should('exist');

    // Nuevo administrador
    cy.contains('a,button', 'Nuevo').click();
    cy.url().should('include', '/admin/admins/create');
    cy.contains('Nuevo administrador').should('exist');

    // Llenar formulario
    cy.get('input[name="name"]').type(testAdminName);
    cy.get('input[name="email"]').type(testAdminEmail);
    cy.get('input[name="password"]').type(testAdminPassword);
    cy.get('input[name="password_confirmation"]').type(testAdminPassword);

    // Marcar Admin Master (ojo con el hidden + checkbox)
    cy.get('input[name="is_master_admin"][type="checkbox"]').check({ force: true });

    // Guardar
    cy.contains('button', 'Guardar').click();

    // Volvemos al listado de admins
    cy.url().should('include', '/admin/admins');
    cy.contains('table tr', testAdminEmail).should('exist');

    // Salir
    logout();
  });

  // 2) Login con Manuel (Admin Master) y salir
  it('Permite iniciar sesión con el admin Manuel (master) y luego salir', () => {
    loginAs(testAdminEmail, testAdminPassword);

    // Verificamos que está logueado (por ejemplo, que ve su correo en pantalla)
    cy.contains(testAdminEmail).should('exist');

    // Salir
    logout();
  });

  // 3) Como admin master original, quitar Admin Master a Manuel
  it('Quita el rol de Admin Master al admin Manuel', () => {
    loginAs(masterEmail, masterPassword);

    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');

    // Editar al admin creado
    cy.contains('tr', testAdminEmail).within(() => {
      cy.contains('a', 'Editar').click();
    });

    cy.url().should('include', '/admin/admins/');

    // Desmarcar Admin Master
    cy.get('input[name="is_master_admin"][type="checkbox"]').uncheck({ force: true });

    // Guardar cambios
    cy.contains('button', 'Guardar').click();

    cy.url().should('include', '/admin/admins');
    cy.contains('table tr', testAdminEmail).should('exist'); // sigue existiendo, pero sin ser master

    // Salir
    logout();
  });

  // 4) Volver a entrar con Manuel (ya sin ser Admin Master) y salir
  it('Permite iniciar sesión con Manuel sin ser Admin Master', () => {
    loginAs(testAdminEmail, testAdminPassword);

    // Sigue pudiendo entrar al panel
    cy.contains('Panel de Administración').should('exist');

    // Salir otra vez
    logout();
  });
    // 5) Eliminar al admin Manuel y verificar que ya no puede iniciar sesión
  it('Elimina al admin Manuel y verifica que no puede iniciar sesión', () => {
    // 1. Entrar como Admin Master original
    loginAs(masterEmail, masterPassword);

    // Ir al módulo Administradores
    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');

    // 2. Buscar la fila de Manuel y hacer clic en Eliminar
    // (por si hay confirm(), lo aceptamos)
    cy.on('window:confirm', () => true);

    cy.contains('tr', testAdminEmail).within(() => {
      cy.contains('a,button', 'Eliminar').click();
    });

    // 3. Verificar que YA NO existe en la tabla
    cy.contains('table tr', testAdminEmail).should('not.exist');

    // 4. Salir del panel (Admin Master)
    logout();

    // 5. Intentar iniciar sesión con Manuel (ya eliminado)
    cy.visit(`${baseUrl}/admin/login`);

    cy.get('input[name="email"]').clear().type(testAdminEmail);
    cy.get('input[name="password"]').clear().type(testAdminPassword);

    cy.contains('button', 'Entrar al panel').click();

    // Debe seguir en la pantalla de login (no debe entrar al panel)
    cy.url().should('include', '/admin/login');
    cy.contains('button', 'Entrar al panel').should('exist');
    cy.contains('Panel de Administración').should('not.exist');
    // si tu login muestra un mensaje de error lo puedes validar aquí, por ej.:
    // cy.contains('Estas credenciales no coinciden').should('exist');
  });

});
