describe('test text display or not device automatically inventoried', () => {
  it('computer and storage manually added', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.runFusionInventory();
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-hardwareinventory"] > .dropdown').click();
    cy.get('[href="/view/computers"]').click();
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').type('test computer');
    cy.get('[data-cy="form-button-save-viewid"] > span').click();

    cy.get('label > .ui').should('have.text', 'Information');
    cy.get('.green.label').should('not.exist');

    cy.get('[data-cy="menu-main-components"] > .dropdown').click();
    cy.get('[href="/view/devices/storages"]').click();
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').type('test storage');
    cy.get('[data-cy="form-button-save-viewid"] > span').click();

    cy.get('.green.label').should('not.exist');

    /* ==== End Cypress Studio ==== */
  });

  it('automatic inventory computer and storage', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-hardwareinventory"] > .dropdown').click();
    cy.get('[href="/view/computers"]').click();
    cy.get('.thread > :nth-child(1) > .ui > .dropdown').click();
    cy.get('[data-value="2"]').click();
    cy.get('#search-input > input').type('ipa');
    cy.get('.thread > :nth-child(3) > .ui').click();
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'ipa');
    cy.get('.labeled > .tiny').click();
    
    cy.get('label > .ui').should('have.text', 'Information');
    cy.get('.green.label').should('be.visible');
    cy.get('.field > :nth-child(5)').should('contain', 'Last automatic inventory');
    
    cy.get('[data-cy="menu-main-components"] > .dropdown').click();
    cy.get('[href="/view/devices/storages"]').click();
    cy.get('.thread > :nth-child(1) > .ui > .dropdown').click();
    cy.get('.scrollhint > [data-value="1"]').click();
    cy.get('#search-input > input').type('nvme0');
    cy.get('.thread > :nth-child(3) > .ui').click();
    cy.get('.tiny > .id').click();

    cy.get('label > .ui').should('have.text', 'Information');
    cy.get('.green.label').should('be.visible');
    cy.get('.field > :nth-child(5)').should('contain', 'Last automatic inventory');
    /* ==== End Cypress Studio ==== */
  });
})
