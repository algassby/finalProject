import useProduct from "../hooks/useProduct";

const Product = ({ setRoute, data: product }: any) => {
  const { quantity, message, loading, setQuantity, addProduct } =
    useProduct(product);

  return (
    <div>
      {loading && <div>Loading....</div>}
      {message && <p className="message">{message}</p>}
      <div onClick={() => setRoute({ route: "home" })}>Retour</div>
      <div>
        <div className="product">
          <img src={product.image} alt="" />
          <p>Figurine de {product.name}</p>
          <p>Quantitée {product.quantity}</p>
        </div>
      </div>
      <hr />
      <input
        type="number"
        value={quantity}
        onChange={(e) => setQuantity(Number(e.target.value))}
        placeholder="Quantité à ajouter"
      />
      <button onClick={addProduct}>Ajouter au panier</button>
    </div>
  );
};

export default Product;
