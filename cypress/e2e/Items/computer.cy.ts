describe('test computer items', () => {
  beforeEach(() =>
  {
    cy.login('admin', 'adminIT');
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('menu to item list', function() {
    cy.dbReset();
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/home');
    cy.get('[data-cy="menu-main-hardwareinventory"]').click();
    cy.get('[href="/view/computers"]').should('have.text', 'Computers');
    cy.get('[href="/view/computers"]').click();
    cy.get('[style="display: flex; justify-content: space-between"] > :nth-child(1) > .content > span').should('have.text', 'Fusion Resolve IT - Computers');
    cy.get('.sub').should('have.text', '0 items');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('creation + new', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-button-new"] > .ui').should('have.text', '\n          \n          New\n          ');
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').type('LPT001');
    cy.get('[data-cy="form-field-user"] > .search').click();
    cy.get('[data-cy="form-field-user"] > .menu > .item').click();
    cy.get('[data-cy="form-field-serial"]').type('XXXRT4T');
    cy.get('[data-cy="form-button-save-new"] > span').click();
    cy.get('body').click();
    cy.get('.sub').should('have.text', '1 items');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('body').click();
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('creation + see', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-button-new"] > .ui > span').should('have.text', 'New');
    cy.get('[data-cy="search-button-new"] > .ui > span').click();
    cy.get('[data-cy="form-field-name"]').type('LPT002');
    cy.get('[data-cy="form-field-serial"]').type('YYYT53R');
    cy.get('[data-cy="form-button-save-viewid"]').should('have.text', '\n            Voir\n          ');
    cy.get('[data-cy="form-button-save-viewid"] > span').click();
    cy.get('.pageheader').click();
    cy.get('[style="display: flex; justify-content: space-between;"] > :nth-child(1)').click();
    cy.get('.pageheader').click();
    cy.get('.content > [data-cy="form-header-id"]').should('have.text', '2');
    cy.get('[data-cy="form-header-title"]').should('have.text', 'LPT002');
    cy.get('[data-cy="form-button-new"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-save"] > span').should('be.visible');
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-field-name"]').should('have.value', 'LPT002');
    cy.get('[data-cy="form-field-serial"]').should('have.value', 'YYYT53R');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('list with the 2 items', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('.sub').should('have.text', '2 items');
    cy.get('body').click();
    cy.get('[data-cy="search-items-item1"] > .collapsing').should('have.class', 'collapsing');
    cy.get('body').click();
    cy.get('[data-cy="search-items-item1"] > .collapsing').should('be.visible');
    cy.get('[data-cy="search-items-item2"] > .collapsing').should('be.visible');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('[data-cy="search-items-item2"] > :nth-child(3)').should('have.text', 'LPT002');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('go item 2, update field name', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-items-item2"] > :nth-child(3)').should('have.text', 'LPT002');
    cy.get('body').click();
    cy.get('[data-cy="search-items-item2"] > :nth-child(2)').should('have.text', '\n            \n              \n                 id\n              \n              2\n            \n          ');
    cy.get('[data-cy="search-items-item2"] > :nth-child(2) > .labeled > .tiny').click();
    cy.get('.content > [data-cy="form-header-id"]').should('have.text', '2');
    cy.get('[data-cy="form-header-title"]').should('have.text', 'LPT002');
    cy.get('[data-cy="form-field-name"]').should('have.value', 'LPT002');
    cy.get('[data-cy="form-field-serial"]').should('have.value', 'YYYT53R');
    cy.get('[data-cy="form-button-new"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-save"]').should('be.visible');
    cy.get('[data-cy="form-button-softdelete"] > .ui').should('be.visible');
    cy.get('[data-cy="form-field-name"]').clear('LPT002.');
    cy.get('[data-cy="form-field-name"]').type('LPT002 bis');
    cy.get('[data-cy="form-button-save"] > span').click();
    cy.get('[data-cy="form-field-name"]').should('have.value', 'LPT002 bis');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('Check field name of item2 updated in list', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-items-item2"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('go in item2 and soft delete it', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-items-item2"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('[data-cy="search-items-item2"] > :nth-child(2) > .labeled > .tiny').click();
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').click();
    cy.get('[data-cy="form-button-new"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-restore"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-delete"] > .ui').should('be.visible');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('Go in search and must have only item 1', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('.sub').should('have.text', '1 items');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('.collapsing').should('be.visible');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('in search go in deleted items and check have only item2', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('body').click();
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('.bordered').should('be.visible');
    cy.get('.bordered').click();
    cy.get('.sub').should('have.text', '1 items');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('.collapsing').should('be.visible');
    cy.get('.labeled > .tiny').should('have.class', 'red');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('go in item2, restore and check is in list items', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('.bordered').click();
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('.tiny > .id').click();
    cy.get('[data-cy="form-field-name"]').should('have.value', 'LPT002 bis');
    cy.get('[data-cy="form-header-title"]').should('have.text', 'LPT002 bis');
    cy.get('.pageheader').click();
    cy.get('[style="display: flex; justify-content: space-between;"]').click();
    cy.get('[style="display: flex; justify-content: space-between;"]').click();
    cy.get('.pageheader').click();
    cy.get('[style="display: flex; justify-content: space-between;"] > :nth-child(1) > .content').click();
    cy.get('.pageheader').click();
    cy.get('[data-cy="form-button-restore"] > .ui > span').should('be.visible');
    cy.get('[data-cy="form-button-delete"] > .ui').should('be.visible');
    cy.get('[data-cy="form-button-restore"] > .ui > span').click();
    cy.get('[href="/view/computers"] > .icon').click();
    cy.get('.sub').should('have.text', '2 items');
    cy.get('[data-cy="search-items-item2"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('go in item1, soft delete it and check in item list not here', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('[data-cy="search-items-item1"] > :nth-child(2) > .labeled > .tiny').click();
    cy.get('[data-cy="form-button-softdelete"] > .ui').should('be.visible');
    cy.get('[data-cy="form-button-softdelete"] > .ui > span').click();
    cy.get('[data-cy="form-button-delete"] > .ui').should('be.visible');
    cy.get('[href="/view/computers"] > .icon').click();
    cy.get('.sub').should('have.text', '1 items');
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('.collapsing').should('be.visible');
    /* ==== End Cypress Studio ==== */
  });

  /* ==== Test Created with Cypress Studio ==== */
  it('delete item1, so not present anywhere', function() {
    /* ==== Generated with Cypress Studio ==== */
    cy.visit('http://127.0.0.1/view/computers');
    cy.get('.bordered').click();
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT001');
    cy.get('.labeled > .tiny').click();
    cy.get('[data-cy="form-button-delete"] > .ui').should('be.visible');
    cy.get('[data-cy="form-button-delete"] > .ui > span').click();
    cy.get('[data-cy="search-items-item1"] > :nth-child(3)').should('have.text', 'LPT002 bis');
    cy.get('.collapsing').should('be.visible');
    cy.get('.bordered').click();
    cy.get('tfoot > tr > th').should('be.visible');
    cy.get('.sub').should('have.text', '0 items');
    /* ==== End Cypress Studio ==== */
  });
})

