type DateRangeEvent = [string, string];

type BillFilterDateHookReturnType = [
  Ref<string>,
  EventHandler<DateRangeEvent>
];

export const useBillFilterDate = (defaultDateRange: string = ""): BillFilterDateHookReturnType => {
  const dateRange = ref<string>("");
  if(defaultDateRange) {
    dateRange.value = defaultDateRange;
  }

  const handleSelectDateRange: EventHandler<DateRangeEvent> = (e) => {
    console.log(e,999999999)
 
   dateRange.value = e.time
   
 
  };

  return [
    dateRange,
    handleSelectDateRange
  ];
};