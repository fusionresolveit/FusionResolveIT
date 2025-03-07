describe('test login in the app', () => {
  // beforeEach(() =>
  //   {
  //     cy.dbReset();
  //   })

  /* ==== Test Created with Cypress Studio ==== */
  it('login', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login-label"]').should('have.text', 'Login');
    cy.get('[data-cy="login-sso-label"]').should('have.text', 'Auto-login / SSO');
    cy.get('[data-cy="login-login"]').clear();
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-user-name"]').should('have.text', 'Administrator');
    cy.get('[data-cy="menu-items"] > :nth-child(1) > .menulink > span').should('have.text', 'ITAM - Hardware inventory');
    /* ==== End Cypress Studio ==== */
  });
})