describe('template spec', () => {

  /* ==== Test Created with Cypress Studio ==== */
  it('Create account', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').clear('a');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-userdata"]').click();
    cy.get('[href="/view/users"]').click();
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').clear('s');
    cy.get('[data-cy="form-field-name"]').type('s.rogers');
    cy.get('[data-cy="form-field-lastname"]').clear();
    cy.get('[data-cy="form-field-lastname"]').type('ROGERS');
    cy.get('[data-cy="form-field-firstname"]').clear();
    cy.get('[data-cy="form-field-firstname"]').type('Steve');
    cy.get('[data-cy="form-field-new_password"]').clear('te');
    cy.get('[data-cy="form-field-new_password"]').type('test999');
    cy.get('[data-cy="form-field-new_password_verification"]').clear();
    cy.get('[data-cy="form-field-new_password_verification"]').type('test999');
    cy.get('[data-cy="form-field-is_active"] > [type="checkbox"]').check();
    cy.get('[data-cy="form-button-save-viewid"] > span').click();
    cy.get('[href*="authorization"]').click();
    cy.get('[data-cy="form-field-entity"] > .search').click();
    cy.get('[data-cy="form-field-entity"] > .menu > .item').click();
    cy.get('[data-cy="form-field-profile"] > .search').click();
    cy.get('[data-cy="form-field-profile"] > .menu > .item').click();
    cy.get('[data-cy="form-field-is_recursive"] > [type="checkbox"]').check();
    cy.get('.four > .ui').click();
    cy.get('tr > :nth-child(1) > a').should('have.text', 'super-admin');
    cy.get('.sign').click();
    cy.get('[data-cy="login-login"]').clear('s');
    cy.get('[data-cy="login-login"]').type('s.rogers');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('test999');
    cy.get('[data-cy="login-submit"]').click();
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('update password', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').clear('a');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-userdata"]').click();
    cy.get('[href="/view/users"]').click();
    cy.get('[data-cy="search-items-item2"] > :nth-child(2) > .labeled > .tiny > .id').click();
    cy.get('[data-cy="form-field-new_password"]').clear('te');
    cy.get('[data-cy="form-field-new_password"]').type('test555');
    cy.get('[data-cy="form-field-new_password_verification"]').clear();
    cy.get('[data-cy="form-field-new_password_verification"]').type('test555');
    cy.get('[data-cy="form-button-save"] > span').click();
    cy.get('.sign').click();
    cy.get('[data-cy="login-login"]').clear('s');
    cy.get('[data-cy="login-login"]').type('s.rogers');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('test555');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[style="display: flex; justify-content: space-between"] > :nth-child(1) > .content > span').should('have.text', 'Fusion Resolve IT - Home');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('delete user', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('.form > :nth-child(1)').click();
    cy.get('[data-cy="login-login"]').clear('a');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-userdata"]').click();
    cy.get('[href="/view/users"]').click();
    cy.get('[data-cy="search-items-item2"] > :nth-child(4)').should('have.text', 'Steve ROGERS');
    cy.get('[data-cy="search-items-item2"] > :nth-child(2) > .labeled > .tiny').click();
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').click();
    cy.get('[data-cy="form-button-delete"] > .ui > span').should('have.text', 'Delete');
    cy.get('[data-cy="form-button-delete"] > .ui > span').click();
    cy.get('.sub').should('have.text', '1 items');
    /* ==== End Cypress Studio ==== */
  });
})
