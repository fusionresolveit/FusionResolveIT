describe('Rights of Knowledge base article', () => {
  beforeEach(() =>
  {
    cy.login('admin', 'adminIT');
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('Create article', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').click();
    cy.get('[data-cy="form-field-name"]').type('FusionInventory agent.cfg');
    cy.get('.toastui-editor-mode-switch > :nth-child(1)').click();
    cy.get('.toastui-editor-md-container > .toastui-editor > .ProseMirror').click();
    cy.get('.toastui-editor-md-container > .toastui-editor > .ProseMirror').type("## Generic directives{enter}" +
      "{enter}" +
      "* **server**{enter}{backspace}{backspace}" +
      "    Specifies the server to use both as a controller for the agent, and as a recipient for task execution output.{enter}" +
      "    If the given value start with http:// or https://, it is assumed to be an URL, and used directly. Otherwise, it is assumed to be an hostname, and interpreted as http://hostname/ocsinventory.{enter}" +
      "    Multiple values can be specified, using a comma as a separator.{enter}" +
      "* **delaytime**{enter}{backspace}{backspace}" +
      "    Specifies the upper limit, in seconds, for the initial delay before contacting the control server. The default is 3600.{enter}" +
      "    The actual delay is computed randomly between TIME / 2 and TIME seconds.{enter}" +
      "    This directive is used for initial contact only, and ignored thereafter in favor of server-provided value (PROLOG\_FREQ).{enter}" +
      "* **lazy**{enter}{backspace}{backspace}" +
      "    Do not contact the control server before next scheduled time.{enter}" +
      "    This directive is used when the agent is run in the foreground (not as a daemon) only.{enter}" +
      "* **no-task**{enter}{backspace}{backspace}" +
      "    Disables given task.{enter}" +
      "    Multiple values can be specified, using a comma as a separator.{enter}" +
      "* **proxy**{enter}{backspace}{backspace}" +
      "    Specifies the URL of the HTTP proxy to use. By default, the agent uses HTTP\_PROXY environment variable.{enter}" +
      "* **user**{enter}{backspace}{backspace}" +
      "    Specifies the user to use for HTTP authentication on the server.{enter}" +
      "* **password**{enter}{backspace}{backspace}" +
      "    Specifies the password to use for HTTP authentication on the server.{enter}" +
      "* **ca-cert-dir**{enter}{backspace}{backspace}" +
      "    Specifies the directory containing indexed Certification Authority (CA) certificates.{enter}" +
      "* **ca-cert-file**{enter}{backspace}{backspace}" +
      "    Specifies the file containing aggregated Certification Authority (CA) certificates.{enter}" +
      "* **no-ssl-check**{enter}{backspace}{backspace}" +
      "    Disables server SSL certificate validation. The default is 0 (false).{enter}" +
      "* **timeout**{enter}{backspace}{backspace}" +
      "    Specifies a timeout, in seconds, for server connections.{enter}" +
      "* **no-httpd**{enter}{backspace}{backspace}" +
      "    Disables the embedded web interface, used to receive execution requests from the server. The default is 0 (false).{enter}");
    cy.get('[data-cy="form-button-save-viewid"] > span').click();
  });

  it('Set right to entity', function() {
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[href="/view/knowledgebasearticles/1/entityview"]').click();
    cy.get('[data-cy="form-field-entity"] > .search').click();
    cy.get('[data-cy="form-field-entity"] > .menu > .item').click();
    cy.get('[data-cy="form-field-is_recursive"] > [type="checkbox"]').check();
    cy.get('.form > .labeled').click();
    cy.get('tbody > tr > :nth-child(2)').should('have.text', 'main');
    cy.get('tr > :nth-child(3) > .toggle').should('have.class', 'on');
    cy.get('[data-cy="back-to-home"]').click();
    cy.get(':nth-child(5) > .horizontal > .content > .header').click();
    cy.get(':nth-child(5) > .horizontal > .content > .header').should('have.text', 'FusionInventory agent.cfg');
    cy.get('.description > .ui').click();
    cy.get('ul > :nth-child(1)').click();

    cy.get('ul > :nth-child(1)').should(
      'have.text',
      'server\nSpecifies the server to use both as a controller for the agent, and as a recipient for task execution output.\nIf the given value start with http:// or https://, it is assumed to be an URL, and used directly. Otherwise, it is assumed to be an hostname, and interpreted as http://hostname/ocsinventory.\nMultiple values can be specified, using a comma as a separator.'
    );
  });

  it('Remove right to entity', function() {
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[href="/view/knowledgebasearticles/1/entityview"] > .ui').click();
    cy.get('.negative').click();
  });

  it('Set right to profile', function() {
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[href="/view/knowledgebasearticles/1/profileview"]').click();
    cy.get('[data-cy="form-field-profile"] > .search').click();
    cy.get('[data-cy="form-field-profile"] > .menu > .item').click();
    cy.get('.form > .labeled > span').click();
    cy.get('[data-cy="back-to-home"]').click();
    cy.get(':nth-child(5) > .horizontal > .content > .header').click();
    cy.get(':nth-child(5) > .horizontal > .content > .header').should('have.text', 'FusionInventory agent.cfg');
    cy.get('.description > .ui').click();
    cy.get('ul > :nth-child(2)').click();
    cy.get('ul > :nth-child(2)').should('have.text', 'delaytime\nSpecifies the upper limit, in seconds, for the initial delay before contacting the control server. The default is 3600.\nThe actual delay is computed randomly between TIME / 2 and TIME seconds.\nThis directive is used for initial contact only, and ignored thereafter in favor of server-provided value (PROLOG_FREQ).');
  });

  it('Remove right to profile', function() {
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[href="/view/knowledgebasearticles/1/profileview"]').click();
    cy.get('.negative').click();
  });

  it('Set right to user', function() {    
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[href="/view/knowledgebasearticles/1/userview"] > .ui').click();
    cy.get('[data-cy="form-field-user"] > .search').click();
    cy.get('[data-cy="form-field-user"] > .menu > .item').click();
    cy.get('.form > .labeled > span').click();
    cy.get('tbody > tr > :nth-child(2)').should('have.text', 'admin');
    cy.get('[data-cy="back-to-home"]').click();
    cy.get('.description > .ui').click();
    cy.get('ul > :nth-child(11)').click();
    cy.get('ul > :nth-child(11)').should('have.text', 'timeout\nSpecifies a timeout, in seconds, for server connections.');
  });

  it('Delete the article', function() {
    cy.visit('http://127.0.0.1/view/home'); 
    cy.get(':nth-child(10) > .menulink > span').click();
    cy.get('[href="/view/knowledgebasearticles"]').click();
    cy.get('.labeled > .tiny').click();
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').click();
    cy.get('[data-cy="form-button-delete"] > .ui > span').click();
    /* ==== End Cypress Studio ==== */
  });
})

