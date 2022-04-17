describe('Cypress load', () => {
    it('load all product', () => {
        cy.intercept('/api/products').as('products1')
        cy.visit('http://localhost:3000')
        cy.wait('@products1').then((interception) => {
            cy.get('img').should('have.length', 20)
        })
    })

    it('Add product to the cart', () => {
        cy.intercept('/api/products').as('products2')
        cy.visit('http://localhost:3000')
        cy.wait('@products2')
        cy.get('.product').first().click({ force: true })
        cy.get("button").click({ force: true });
        cy.intercept('/api/cart/*').as('cart1')
        cy.wait('@cart1', { timeout: { timeout: 15000 } })
        cy.get('.message').first().contains("Enregistré dans le panier")
    })

    it('Error add product to the cart', () => {
        cy.intercept('/api/products').as('products3')
        cy.visit('http://localhost:3000')
        cy.wait('@products3', { timeout: { timeout: 15000 } })
        cy.get('.product').first().click({ force: true })
        cy.get('input').type(50, { force: true })
        cy.get("button").click({ force: true });
        cy.intercept('/api/cart/*').as('cart2')
        cy.wait(6000)
        cy.get('.message').first().contains("Trop de quantité")
    })

    it('Delete product to the cart', () => {
        cy.intercept('/api/products').as('products4')
        cy.visit('http://localhost:3000')
        cy.wait('@products4', { timeout: { timeout: 15000 } })
        cy.get('.goPanier').click({ force: true })
        cy.wait(6000)
        cy.get("button").click({ force: true });
        cy.intercept('/api/cart/*').as('cart3')
        cy.wait(6000)
        cy.get('.message').first().contains("Produit bien supprimé")
    })

})