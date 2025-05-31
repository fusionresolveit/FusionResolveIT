describe('Dark mode', () => {
  /* ==== Test Created with Cypress Studio ==== */
  it('Test switch to dark mode', function() {
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('.pageheader').should('not.have.class', 'inverted');
    cy.get('.moon').click();
    cy.get('.pageheader').should('have.class', 'inverted');
    cy.reload()
    cy.get('.pageheader').should('have.class', 'inverted');
    cy.get('[data-cy="menu-main-hardwareinventory"]').click();
    cy.get('.pageheader').should('have.class', 'inverted');
  });

  it('Test switch to light mode', function() {
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('.pageheader').should('have.class', 'inverted');
    cy.get('.sun').click();
    cy.get('.pageheader').should('not.have.class', 'inverted');
    cy.reload()
    cy.get('.pageheader').should('not.have.class', 'inverted');
    cy.get('[data-cy="menu-main-hardwareinventory"]').click();
    cy.get('.pageheader').should('not.have.class', 'inverted');
  });
})