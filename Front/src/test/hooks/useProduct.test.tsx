import { rest } from "msw";
import { setupServer } from "msw/node";
import { renderHook, act } from '@testing-library/react-hooks'
import useCart from "../../hooks/useCart";
import useProduct from "../../hooks/useProduct";

const server = setupServer(
    rest.get(
        "http://localhost:8000/api/cart",
        (req, res, ctx) => {
            return res(
                ctx.json({
                    products: [{
                        id: 3,
                        name: 'Summer Smith',
                        price: '15',
                        quantity: 5,
                        image: 'https://rickandmortyapi.com/api/character/avatar/3.jpeg'
                    },
                    {
                        id: 15,
                        name: 'Alien Rick',
                        price: '20',
                        quantity: 20,
                        image: 'https://rickandmortyapi.com/api/character/avatar/15.jpeg'
                    },
                    {
                        id: 15,
                        name: 'Alien Rick',
                        price: '20',
                        quantity: 20,
                        image: 'https://rickandmortyapi.com/api/character/avatar/15.jpeg'
                    }]
                }))
        }),
    rest.post(
        "http://localhost:8000/api/cart/15",
        (req, res, ctx) => {
            // @ts-ignore  
            if (req.body.quantity == 100) {
                return res(
                    ctx.json({ 'error': 'quantity' })
                )
            }
            else {
                return res(
                    ctx.json({
                        products: [
                            {
                                id: 15,
                                name: 'Alien Rick',
                                price: '20',
                                quantity: 20,
                                image: 'https://rickandmortyapi.com/api/character/avatar/15.jpeg'
                            }]
                    }))
            }
        }),
    // remove
);

beforeEach(() => jest.setTimeout(70000))
beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());

jest.setTimeout(30000);

test("Call addProduct Good quantity", async () => {
    const { result } = renderHook(() => useCart());
    const { loading, loadCart } = result.current;
    expect(loading).toEqual(true);
    await act(async () => {
        await loadCart()
    });
    const { products } = result.current;
    const result2 = renderHook(() => useProduct(products[1])).result;
    const { setQuantity } = result2.current;
    await act(async () => {
        setQuantity(1)
    });
    const { addProduct } = result2.current;
    await act(async () => {
        await addProduct()
    });
    const message2 = result2.current.message;
    expect(message2).toBe("Enregistré dans le panier")
})

test("Call addProduct quantity bad", async () => {
    const { result } = renderHook(() => useCart());
    const { loading, loadCart } = result.current;
    expect(loading).toEqual(true);
    await act(async () => {
        await loadCart()
    });
    const { products } = result.current;
    const result2 = renderHook(() => useProduct(products[1])).result;
    const { setQuantity } = result2.current;
    await act(async () => {
        setQuantity(100)
    });
    const { addProduct } = result2.current;
    await act(async () => {
        await addProduct()
    });
    const products2 = result2.current;
    expect(products2.message).toBe("Trop de quantité")
})

