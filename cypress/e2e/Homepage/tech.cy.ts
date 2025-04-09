describe('Test tech homepage', () => {
  beforeEach(() =>
  {
    cy.login('admin', 'adminIT');
  });

  it('test', () => {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/home');
    cy.get('[data-cy="home-user-switchtotech"] > form > button > span').should('have.text', 'Homepage tech');
    cy.get('[data-cy="home-user-switchtotech"]').click();
    cy.get('[data-cy="home-tech-card-new-tickets"] > .content > .header > .ui > span').should('have.text', 'New tickets');
    cy.get('[data-cy="home-tech-switchtotech"] > form > button > span').should('have.text', 'Homepage user');
    cy.get('[data-cy="home-tech-switchtotech"]').click();
    /* ==== End Cypress Studio ==== */
  });

});
