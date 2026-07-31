import { getRandomString } from "@/utils/helper";
import { fillProductInfo, ProductItem, ProductItemWithOppo } from "../components/product";

export const useProductData = () => {
  const productListData = ref<ProductItemWithOppo[]>([]);

  const addProductEvent = getRandomString();
  const editProductEvent = getRandomString();

  const handleDelProduct = (index: number) => {
    productListData.value.splice(index, 1);
  };

  const totalCount = 0

  const handleUpdateProduct = (data: Record<string, string>) => {
    const { count, price,discount, total_price, remark, discount_price, unique } = data;
    const product = productListData.value.find(item => item.unique === unique);
    if (product) {
      product.count = Number(count);
      product.price = Number(price);
      product.discount = Number(discount);
      product.total_price = Number(total_price);
      product.remark = remark;
      product.discount_price = Number(discount_price);
    }
  };

  const handleAddProduct = (data: ProductItem[]) => {
    const productMap = new Map<string, ProductItemWithOppo>();
    productListData.value.forEach((item) => {
      productMap.set(item.unique, item);
    });
    data.forEach((item: ProductItem) => {
      if (!productMap.has(item.unique)) {
        productListData.value.push(fillProductInfo(item));
      }
    });
  };

  uni.$on(addProductEvent, handleAddProduct);
  uni.$on(editProductEvent, handleUpdateProduct);

  onUnmounted(() => {
    uni.$off(addProductEvent, handleAddProduct);
    uni.$off(editProductEvent, handleUpdateProduct);
  });

  return {
    productListData,
    addProductEvent,
    editProductEvent,

    handleAddProduct,
    handleUpdateProduct,
    handleDelProduct,
  } as const;
};

export const useProductReducer = () => {
  const productData = useProductData();
  const { productListData, addProductEvent, editProductEvent } = productData;

  const addProductUrl = computed(() => {
    const unique = productListData.value.map(item => item.unique).join(",");
    return `/pages/customer/opportunity/add-product?event_name=${addProductEvent}&unique=${unique}`;
  });

  const totalPrice = computed(() => {
    return productListData.value.reduce((acc: number, item: typeof productListData.value[0]) => {
    
      const totalPrice = typeof item.total_price === "string" ? Number(item.total_price) : item.total_price;
      return acc + totalPrice;
    }, 0);
  });

  const totalCount = computed(() => {
    return productListData.value.reduce((acc: number, item: typeof productListData.value[0]) => {
    
      const totalCount = typeof item.total_price === "string" ? Number(item.count) : item.count;
      return acc + totalCount;
    }, 0);
  });



  const handleGoEditProductPage = (index: number) => {
    const { count, discount, price, total_price, remark, unique, discount_price } = productListData.value[index];

    uni.navigateTo({
      url: `/pages/customer/opportunity/edit-price?event_name=${editProductEvent}&unique=${unique}&count=${count}&discount=${discount}&price=${price}&total_price=${total_price}&remark=${remark}&discount_price=${discount_price}`
    });
  };

  return {
    addProductUrl,
    totalPrice,
    totalCount,
    handleGoEditProductPage,
    ...productData
  } as const;
};
