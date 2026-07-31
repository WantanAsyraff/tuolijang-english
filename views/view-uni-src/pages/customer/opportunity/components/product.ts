export interface ProductItem {
  id: number;
  image: string;
  name: string;
  sku: string;
  count: number;
  ot_price: string;
  price: string;
  cost: string;
  selected: boolean;
  unique: string;
  remark?: string;
  discount?: number;
  product_name?: string;
}

export interface ProductItemWithOppo extends Omit<ProductItem, "selected"> {
  remark: string;
  discount: number;
  discount_price: number;
  total_price: number;
}

export const fillProductInfo = (data: ProductItem): ProductItemWithOppo => {
  return {
    ...data,
    count: data.count || 1,
    name: data.product_name || data.name,
    remark: data.remark || "",
    ot_price: Number(data.ot_price) ? data.ot_price : data.price,
    discount: data.discount || 100,
    discount_price: Number(data.price),
    total_price: Number(data.price),
  };
};

export const processSubmitProductData = (data: ProductItemWithOppo[]) => {
  return data.map((i) => {
    const { unique, count, discount, discount_price, total_price, ot_price, price, remark, sku } = i;
    return {
      unique,
      price: Number(discount_price ?? price ?? 0),
      count,
      discount,
      total_price,
      ot_price,
      remark,
      sku
    };
  });
};
