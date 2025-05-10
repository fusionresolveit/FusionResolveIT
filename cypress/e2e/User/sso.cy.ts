describe('template spec', () => {
  // it('reset dababase', function() {
  //   cy.dbReset();
  // });

  /* ==== Test Created with Cypress Studio ==== */
  it('configure SSO no rule', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').clear('a');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-userdata"] > span').click();
    cy.get('[href="/view/authssos"]').should('have.text', 'Authentication SSO');
    cy.get('[href="/view/authssos"]').click();
    cy.get('[data-cy="search-button-new"] > .ui').click();
    cy.get('[data-cy="form-field-name"]').clear('F');
    cy.get('[data-cy="form-field-name"]').type('FusionResolveIT');
    cy.get('[data-cy="form-field-is_active"] > [type="checkbox"]').check();
    cy.get('.row > :nth-child(1) > :nth-child(3)').click();
    cy.get('[data-cy="form-field-provider"] > .search').click();
    cy.get('[data-value="keycloak"]').click();
    cy.get('[data-cy="form-field-callbackid"]').clear('6');
    cy.get('[data-cy="form-field-callbackid"]').type('67fecbfb9b883');
    cy.get('[data-cy="form-field-applicationid"]').clear('F');
    cy.get('[data-cy="form-field-applicationid"]').type('FusionResolveIT');
    cy.get('[data-cy="form-field-applicationpublic"]').clear('F');
    cy.get('[data-cy="form-field-applicationpublic"]').type('FusionResolveIT');
    cy.get('[data-cy="form-field-baseurl"]').clear('h');
    cy.get('[data-cy="form-field-baseurl"]').type('http://localhost:8080');
    cy.get('[data-cy="form-field-realm"]').clear('F');
    cy.get('[data-cy="form-field-realm"]').type('FusionResolveIT');
    cy.get('[data-cy="form-button-save-viewid"]').click();
    cy.get('.sign').click();
    cy.get('[data-cy="sso-FusionResolveIT"] > .ui > span').should('have.text', 'FusionResolveIT');
    cy.get('[data-cy="sso-FusionResolveIT"] > .ui > span').click();
    cy.visit('http://127.0.0.1');
    cy.visit('http://127.0.0.1/');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('create profile normal and rule', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="login-login"]').clear('a');
    cy.get('[data-cy="login-login"]').type('admin');
    cy.get('[data-cy="login-password"]').clear();
    cy.get('[data-cy="login-password"]').type('adminIT');
    cy.get('[data-cy="login-submit"]').click();
    cy.get('[data-cy="menu-main-userdata"] > span').click();
    cy.get('[href="/view/profiles"]').should('have.text', 'Profiles');
    cy.get('[href="/view/profiles"]').click();
    cy.get('[data-cy="search-button-new"] > .ui').click();
    cy.get('[data-cy="form-field-name"]').clear('no');
    cy.get('[data-cy="form-field-name"]').type('normal');
    cy.get('[data-cy="form-field-interface"] > .search').click();
    cy.get('[data-value="helpdesk"] > span').click();
    cy.get('[data-cy="form-button-save-viewid"] > span').click();
    cy.get('[data-cy="menu-main-userdata"] > span').click();
    cy.get('[href="/view/rules/users"]').should('have.text', 'Rules for users');
    cy.get('[href="/view/rules/users"]').click();
    cy.get('[data-cy="search-button-new"] > .ui').click();
    cy.get('[data-cy="form-field-name"]').clear('S');
    cy.get('[data-cy="form-field-name"]').type('SSO normal profile');
    cy.get('[data-cy="form-field-match"] > .search').click();
    cy.get('[data-cy="form-field-match"] > .menu > .selected > span').click();
    cy.get('[data-cy="form-field-is_active"] > [type="checkbox"]').check();
    cy.get('[data-cy="form-button-save-viewid"] > span').click();
    cy.get('[href="/view/rules/users/1/criteria"] > .icon').click();
    cy.get('[href="/view/rules/users/1/criteria/new"] > .ui').click();
    cy.get('.three > :nth-child(1) > .ui > .search').click();
    cy.get('[data-value="authsso"]').click();
    cy.get('#conditiondiv > .search').click();
    cy.get('#conditiondiv > .menu > .selected').click();
    cy.get('#patterndiv > .search').click();
    cy.get('#patterndiv > .menu > .item').click();
    cy.get('#submitbutton').click();
    cy.get('.three > :nth-child(1) > .text').should('have.text', 'authsso');
    cy.get('.three > :nth-child(3) > .text').should('have.text', 'FusionResolveIT');
    cy.get('[href="/view/rules/users/1/actions"] > .ui').click();
    cy.get('[href="/view/rules/users/1/actions/new"] > .ui').click();
    cy.get('.three > :nth-child(1) > .ui > .search').type('profile');
    cy.wait(1000);
    cy.get('.three > :nth-child(1) > .ui > .menu > .item').click();
    cy.get('.three > :nth-child(2)').click();
    cy.get('#action_type > .search').click();
    cy.get('#action_type > .menu > .item').click();
    cy.get('#valuediv > .search').click();
    cy.get('[data-value="2"]').click();
    cy.get('#submitbutton').click();
    cy.get('[data-cy="form-field-value"] > span').should('have.text', 'Default Profile');
    cy.get('.three > :nth-child(3) > .text').should('have.text', 'normal');
    cy.get('[href="/view/rules/users/1/actions/new"] > .ui').click();
    // cy.get('.three > :nth-child(1) > .ui > .search').clear('e');
    cy.get('.three > :nth-child(1) > .ui > .search').type('ent');
    cy.wait(1000);
    cy.get('[data-value="entity"]').click();
    cy.get('#action_type > .search').click();
    cy.get('#action_type > .menu > .item').click();
    cy.get('#valuediv > .search').click();
    cy.get('#valuediv > .menu > .item').click();
    cy.get('#submitbutton').click();
    cy.get('.sign').click();
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('Connect with SSO', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1');
    cy.get('[data-cy="sso-FusionResolveIT"] > .ui > span').should('have.text', 'FusionResolveIT');
    cy.get('[data-cy="sso-FusionResolveIT"] > .ui > span').click();
    cy.get('#username').clear('I');
    cy.get('#username').type('IronMan');
    cy.get('#password').clear();
    cy.get('#password').type('jarvis');
    cy.get('#kc-login').click();
    cy.get('[style="display: flex; justify-content: space-between"] > :nth-child(1) > .content > span').should('have.text', 'Fusion Resolve IT - Home');
    /* ==== End Cypress Studio ==== */
  });
});
