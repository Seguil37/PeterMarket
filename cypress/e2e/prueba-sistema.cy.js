describe('PRUEBA DE SISTEMA COMPLETA - Peter Market', () => {

  const baseUrl = 'http://127.0.0.1:8000';

  // ============================================================
  // ===============   USUARIOS Y DATOS  =======================
  // ============================================================

  const masterEmail = 'admin@tuapp.com';
  const masterPassword = 'clave-super-segura';

  const testAdminName = 'Manuel';
  const testAdminEmail = 'adminprueba@gmail.com';
  const testAdminPassword = '12345678';

  const userName = 'Usuario Cypress';
  const userEmail = 'usuario1347@test.com';
  const userPassword = '12345678';

  const nombreProducto = 'Papel higiénico x12';
  const nuevaDescripcion = 'PRUEBA CYPRESS SISTEMA';

  const checkout = {
    nombre: 'Seguil S.O',
    correo: 'seguilso37@gmail.com',
    direccion: 'Apv san Antonio g-8-2',
    ciudad: 'Cusco',
    referencia: 'casa color lila',
  };

  // ============================================================
  // ===============   HELPERS REUTILIZABLES  ===================
  // ============================================================

  const loginAs = (email, password) => {
    cy.visit(`${baseUrl}/admin/login`);
    cy.get('input[name="email"]').clear().type(email);
    cy.get('input[name="password"]').clear().type(password);
    cy.contains('button', 'Entrar al panel').click();
    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');
  };

  const logoutAdmin = () => {
    cy.get('header').contains('button', 'Salir').click();
    cy.contains('button', 'Entrar al panel').should('exist');
  };

  const logoutUser = () => {
    cy.contains('button, a', 'Salir').click();
    cy.url().should('eq', `${baseUrl}/`);
  };

  // ============================================================
  // ===============   PRUEBA 1: ADMIN MASTER  =================
  // ============================================================

  it('1️⃣ Crea un nuevo admin Manuel como Admin Master', () => {
    loginAs(masterEmail, masterPassword);

    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');
    cy.contains('Administradores').should('exist');

    cy.contains('a,button', 'Nuevo').click();
    cy.url().should('include', '/admin/admins/create');
    cy.contains('Nuevo administrador').should('exist');

    cy.get('input[name="name"]').type(testAdminName);
    cy.get('input[name="email"]').type(testAdminEmail);
    cy.get('input[name="password"]').type(testAdminPassword);
    cy.get('input[name="password_confirmation"]').type(testAdminPassword);

    cy.get('input[name="is_master_admin"][type="checkbox"]').check({ force: true });

    cy.contains('button', 'Guardar').click();

    cy.url().should('include', '/admin/admins');
    cy.contains('table tr', testAdminEmail).should('exist');

    logoutAdmin();
  });

  // ============================================================
  // ===============   PRUEBA 2: LOGIN ADMIN MANUEL  ===========
  // ============================================================

  it('2️⃣ Permite iniciar sesión con el admin Manuel (master) y luego salir', () => {
    cy.visit(`${baseUrl}/admin/login`);
    cy.get('input[name="email"]').clear().type(testAdminEmail);
    cy.get('input[name="password"]').clear().type(testAdminPassword);
    cy.contains('button', 'Entrar al panel').click();

    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');
    cy.contains(testAdminEmail).should('exist');

    logoutAdmin();
  });

  // ============================================================
  // ===============   PRUEBA 3: QUITAR ROL MASTER ==============
  // ============================================================

  it('3️⃣ Quita el rol de Admin Master al admin Manuel', () => {
    loginAs(masterEmail, masterPassword);

    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');

    cy.contains('tr', testAdminEmail).within(() => {
      cy.contains('a', 'Editar').click();
    });

    cy.url().should('include', '/admin/admins/');

    cy.get('input[name="is_master_admin"][type="checkbox"]').uncheck({ force: true });

    cy.contains('button', 'Guardar').click();

    cy.url().should('include', '/admin/admins');
    cy.contains('table tr', testAdminEmail).should('exist');

    logoutAdmin();
  });

  // ============================================================
  // ===============   PRUEBA 4: LOGIN SIN MASTER  ==============
  // ============================================================

  it('4️⃣ Permite iniciar sesión con Manuel sin ser Admin Master', () => {
    cy.visit(`${baseUrl}/admin/login`);
    cy.get('input[name="email"]').clear().type(testAdminEmail);
    cy.get('input[name="password"]').clear().type(testAdminPassword);
    cy.contains('button', 'Entrar al panel').click();

    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');

    logoutAdmin();
  });

  // ============================================================
  // ===============   PRUEBA 5: ELIMINAR ADMIN  ================
  // ============================================================

  it('5️⃣ Elimina al admin Manuel y verifica que no puede iniciar sesión', () => {
    loginAs(masterEmail, masterPassword);

    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');

    cy.on('window:confirm', () => true);

    cy.contains('tr', testAdminEmail).within(() => {
      cy.contains('a,button', 'Eliminar').click();
    });

    cy.contains('table tr', testAdminEmail).should('not.exist');

    logoutAdmin();

    cy.visit(`${baseUrl}/admin/login`);
    cy.get('input[name="email"]').clear().type(testAdminEmail);
    cy.get('input[name="password"]').clear().type(testAdminPassword);
    cy.contains('button', 'Entrar al panel').click();

    cy.url().should('include', '/admin/login');
    cy.contains('button', 'Entrar al panel').should('exist');
    cy.contains('Panel de Administración').should('not.exist');
  });

  // ============================================================
  // ===============   PRUEBA 6: REGISTRO USUARIO  ==============
  // ============================================================

  it('6️⃣ Crea un usuario, cierra sesión y vuelve a iniciar sesión correctamente', () => {
    cy.visit(`${baseUrl}/register`);

    cy.get('input[name="name"]').type(userName);
    cy.get('input[name="email"]').type(userEmail);
    cy.get('input[name="password"]').type(userPassword);
    cy.get('input[name="password_confirmation"]').type(userPassword);

    cy.contains('button', 'Registrar').click();

    cy.url().should('not.include', '/register');
    cy.contains(userName).should('exist');

    logoutUser();

    cy.visit(`${baseUrl}/login`);
    cy.contains('Iniciar sesión').should('exist');

    cy.get('input[name="email"]').clear().type(userEmail);
    cy.get('input[name="password"]').clear().type(userPassword);
    cy.contains('button', /^Entrar$/).click();

    cy.url().should('not.include', '/login');
    cy.contains(userName).should('exist');

    logoutUser();
  });

  // ============================================================
  // ===============   PRUEBA 7: EDITAR PRODUCTO  ===============
  // ============================================================

  it('7️⃣ Cambia la descripción del producto en admin y la ve en la página pública', () => {
    loginAs(masterEmail, masterPassword);

    cy.visit(`${baseUrl}/admin/products`);

    cy.contains('tr', nombreProducto)
      .within(() => {
        cy.contains('a', 'Editar').click();
      });

    cy.get('textarea[name="description"]')
      .clear()
      .type(nuevaDescripcion);

    cy.get('#btnCargarImagen').click();

    cy.contains('Producto actualizado.').should('be.visible');

    logoutAdmin();

    // Verificar en página pública
    cy.visit(baseUrl);
    cy.contains('a', 'Productos').click();

    cy.contains('article', nombreProducto)
      .within(() => {
        cy.contains('a', 'Ver más').click();
      });

    cy.url().should('include', '/products/');
    cy.contains(nuevaDescripcion).should('be.visible');
  });

  // ============================================================
  // ===============   PRUEBA 8: NAVEGACIÓN ADMIN  ==============
  // ============================================================

  it('8️⃣ Navega por todos los módulos del panel admin correctamente', () => {
    loginAs(masterEmail, masterPassword);

    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');

    // Dashboard
    cy.contains('nav a', 'Dashboard').click();
    cy.url().should('include', '/admin');
    cy.contains('Panel de Administración').should('exist');

    // Inventario
    cy.contains('nav a', 'Inventario').click();
    cy.url().should('include', '/admin/inventario');
    cy.contains('Inventario').should('exist');

    // Productos
    cy.contains('nav a', 'Admin').click();
    cy.contains('CRUD productos.').should('exist');
    cy.visit(`${baseUrl}/admin/products`);

    // Administradores
    cy.contains('nav a', 'Administradores').click();
    cy.url().should('include', '/admin/admins');
    cy.contains('Administradores').should('exist');

    // Volver al Dashboard
    cy.contains('nav a', 'Dashboard').click();
    cy.url().should('include', '/admin');

    logoutAdmin();
  });

  // ============================================================
  // ===============   PRUEBA 9: COMPRA COMPLETA  ===============
  // ============================================================

  it('9️⃣ Realiza una compra completa como cliente', () => {
    cy.visit(`${baseUrl}/login`);

    cy.get('input[name="email"]').type(userEmail);
    cy.get('input[name="password"]').type(userPassword);
    cy.contains('button', /^Entrar$/).click();

    cy.contains('a', 'Productos').click();

    cy.contains('article', nombreProducto)
      .within(() => cy.contains('a', 'Ver más').click());

    cy.url().should('include', '/products/');
    cy.contains(nombreProducto).should('exist');

    cy.get('input[name="quantity"]').clear().type('5');

    cy.contains('button', 'Añadir al carrito').click();

    cy.contains('a', 'Carrito').click();
    cy.url().should('include', '/cart');

    cy.get('input[name="customer_name"]').clear().type(checkout.nombre);
    cy.get('input[name="customer_email"]').clear().type(checkout.correo);
    cy.get('input[name="shipping_address"]').clear().type(checkout.direccion);
    cy.get('input[name="shipping_city"]').clear().type(checkout.ciudad);
    cy.get('input[name="shipping_reference"]').clear().type(checkout.referencia);

    cy.contains('label', 'Pago simulado').click({ force: true });

    cy.contains('button', 'Pagar ahora').click();

    cy.contains('Gracias', { timeout: 10000 }).should('exist');

    logoutUser();
  });

});