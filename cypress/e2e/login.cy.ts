describe('test logimn in the app', () => {

  /* ==== Test Created with Cypress Studio ==== */
  it('login', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get(':nth-child(1) > input').click();
    cy.get(':nth-child(1) > label').should('have.text', 'Login');
    cy.get(':nth-child(2) > .header').should('have.text', '\n                \n                  Auto-login / SSO\n                ');
    cy.get(':nth-child(1) > input').clear();
    cy.get(':nth-child(1) > input').type('admin');
    cy.get(':nth-child(2) > input').clear();
    cy.get(':nth-child(2) > input').type('adminIT');
    cy.get(':nth-child(3) > .ui').click();
    cy.get('.left > :nth-child(2)').should('have.text', '\n          Administrator\n          \n            \n          \n        ');
    cy.get(':nth-child(5) > .menu > :nth-child(1) > .menulink').should('have.text', '\n                  \n                  ITAM - Hardware inventory\n                  14\n                ');
    /* ==== End Cypress Studio ==== */
  });
})