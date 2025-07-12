describe('change lang', () => {

})

/* ==== Test Created with Cypress Studio ==== */
it('change lang', function() {
  /* ==== Generated with Cypress Studio ==== */
  cy.visit('127.0.0.1');
  cy.get('[data-cy="login-password-label"]').should('have.text', 'Password');
  cy.get('.ui > .search').click();
  cy.get('[data-value="fr-FR"]').click();
  cy.get('[data-cy="login-password-label"]').should('have.text', 'Mot de passe');
  cy.get('[data-cy="login-login"]').type('admin');
  cy.get('[data-cy="login-password"]').type('adminIT');
  cy.get('[data-cy="login-submit"]').click();
  cy.get('[data-cy="content-page"]').click();
  cy.get('.sub').should('have.text', ' items');
  cy.get('#changeLanguage > .search').click();
  cy.get('[data-value="fr-FR"]').click();
  cy.get('[data-cy="menu-main-hardwareinventory"] > span').should('have.text', 'ITAM - Inventaire matériel');
  cy.get('.sign').click();
  cy.get('[data-cy="login-password-label"]').should('have.text', 'Mot de passe');
  cy.get('[data-cy="login-login"]').type('admin');
  cy.get('[data-cy="login-password"]').type('adminIT');
  cy.get('[data-cy="login-submit"]').click();
  cy.get('[data-cy="menu-main-hardwareinventory"] > span').should('have.text', 'ITAM - Inventaire matériel');
  cy.get('#changeLanguage > .search').click();
  cy.get('#changeLanguage > .menu > .selected').click();
  cy.get('[data-cy="menu-main-hardwareinventory"]').should('have.text', '\n                  \n                  \n                  ITAM - Hardware inventory\n                  14\n                ');
  cy.get('.sign').click();
  cy.get('[data-cy="login-password-label"]').should('have.text', 'Password');
  /* ==== End Cypress Studio ==== */
});