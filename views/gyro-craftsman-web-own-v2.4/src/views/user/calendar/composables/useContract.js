import { ref, getCurrentInstance } from 'vue';

export const useContract = () => {
  const contractEditRef = ref(null);
  const contractFromData = ref({});

  const { proxy } = getCurrentInstance();

  const openContractPanel = (contractId) => {

    Object.assign(contractFromData.value, {
      title: proxy.$t('customer.editcustomer'),
      width: '1100px',
      data: {},
      link_type: 'customer',
      types: 'customer',
      type: 'add'
    });

    if (!contractEditRef.value) return;
    contractEditRef.value.tabIndex = '1'
    contractEditRef.value.tabNumber = 1
    contractEditRef.value.openBox(contractId, 'customer');
  }

  return {
    contractEditRef,
    contractFromData,

    openContractPanel
  };
}