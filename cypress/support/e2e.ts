/// <reference types="cypress" />

Cypress.Commands.add('dbReset', () =>
{
    cy.exec(`php bin/cli reset`);
});

Cypress.Commands.add('login', (username, password) =>
{
    cy.session([username, password], () =>
    {
        cy.visit('http://127.0.0.1');
        cy.get('[data-cy="login-login-label"]').should('have.text', 'Login');
        cy.get('[data-cy="login-sso-label"]').should('have.text', 'Auto-login / SSO');
        cy.get('[data-cy="login-login"]').clear();
        cy.get('[data-cy="login-login"]').type(username);
        cy.get('[data-cy="login-password"]').clear();
        cy.get('[data-cy="login-password"]').type(password);
        cy.get('[data-cy="login-submit"]').click();
    },
    {
        cacheAcrossSpecs: true
    });
});