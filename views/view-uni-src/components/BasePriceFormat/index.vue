<template>
  <view class="price-format">
    <text class="symbol">¥</text>
    <text class="int-part">{{ intPart }}</text>
    <text class="decimal-part">.{{ decimalPart }}</text>
  </view>
</template>

<script setup lang="ts">
const props = defineProps({
  price: {
    type: Number,
    default: 0,
  },
});
const { price } = toRefs(props);

const intPart = computed(() => {
  return Math.floor(price.value);
});

const decimalPart = computed(() => {
  const priceStr = price.value.toString();
  const intPartStr = intPart.value.toString();
  if (intPartStr.length === priceStr.length) {
    return "00";
  }
  return priceStr.slice(intPartStr.length + 1);
});

</script>

<style scoped lang="scss">
@font-face {
  font-family: DIN-Condensed-Bold;
  src: url("@/static/fonts/D-DIN-PRO.ttf") format("truetype");
}

.price-format {
  font-family: DIN-Condensed-Bold;
  font-size: 20rpx;

  .symbol {
    font-size: var(--symbol-size, inherit);
  }

  .int-part {
    font-size: var(--int-part-size, 32rpx);
  }

  .decimal-part {
    font-size: var(--decimal-part-size, inherit);
  }
}
</style>
