<template>
  <view class="form-body">
    <view class="form-item" v-for="item in formConfig" :key="item.key">
      <view class="form-item-label">{{ item.label }}</view>
      <picker mode="multiSelector" v-if="item.type === 'date-time'" :value="item.index" :range="item.range"
        class="form-item-content" @change="handlePickerChange(item, $event)">
        <view class="picker-content">
          {{ formatDatetime(item.index, item.range) }}
          <view class="iconfont icon-jinru-copy" />
        </view>
      </picker>
      <picker mode="multiSelector" v-else-if="item.type === 'hour-time'" :value="item.index" :range="hourTimeRange"
        class="form-item-content" @change="handlePickerChange(item, $event)">
        <view class="picker-content">
          {{ formatHourTime(item.index) }}
          <view class="iconfont icon-jinru-copy" />
        </view>
      </picker>
      <view class="form-item-content" v-else-if="item.type === 'switch'">
        <view class="switch-content">
          <switch :checked="item.checked === 1" @change="handleSwitchChange(item, $event)" />
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
  import moment from 'moment';
  import { hourTimeRange, formatHourTime } from '@/utils/date';

  const props = withDefaults(defineProps<{
    form : any;
    type : "work" | "off-work" | "rest";
    allowNextDay ?: boolean | undefined;
    prefix ?: string;
  }>(), {
    allowNextDay: undefined
  });

  const { form, allowNextDay } = toRefs(props);

  const generateDatetimeRange = (onlyCurrentDay : boolean = false) : string[][] => {
    const dateTimeRange = [
      ['当日'],
      Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0')),
      Array.from({ length: 60 }, (_, i) => i.toString().padStart(2, '0'))
    ];

    if (!onlyCurrentDay) {
      dateTimeRange[0].push('次日');
    }

    return dateTimeRange;
  }

  const formatDatetime = (value : number[], range : any) => {
    const result = [];
    if (value[0] === 1) {
      result.push('次日');
    }

    result.push(range[1][value[1]], ":", range[2][value[2]]);
    return result.join('');
  }

  type FormItemConfig = {
    label : string,
    key : string,
  } & (
      {
        type : "date-time",
        range : string[][],
        dayAfterKey ?: string,
        index : number[],
      } | {
        type : "hour-time",
        index : number[],
      } | {
        type : "switch",
        checked : 1 | 0,
      }
    )

  const getWorkForm = () : FormItemConfig[] => [
    {
      label: "上班时间",
      key: 'work_hours',
      type: 'date-time',
      index: [0, 0, 0],
      range: generateDatetimeRange(true)
    },
    {
      label: "晚到超过多久记为迟到",
      key: "late",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "晚到超过多久记为严重迟到",
      key: "extreme_late",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "晚到超过多久记为半天缺卡",
      key: "late_lack_card",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "最早可提前多久打卡",
      key: "early_card",
      type: "hour-time",
      index: [0, 0]
    }
  ];

  const getOffWorkForm = () : FormItemConfig[] => [
    {
      label: "下班时间",
      key: 'off_hours',
      type: 'date-time',
      dayAfterKey: 'second_day_after',
      index: [0, 0, 0],
      range: generateDatetimeRange()
    },
    {
      label: "提前多久打卡记为早退",
      key: "early_leave",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "提前多久打卡记为半天缺卡",
      key: "early_lack_card",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "最晚可延后多久打卡",
      key: "delay_card",
      type: "hour-time",
      index: [0, 0]
    },
    {
      label: "下班可免打卡",
      key: "free_clock",
      type: "switch",
      checked: 0
    }
  ];

  const getRestForm = () : FormItemConfig[] => [
    {
      label: "休息开始时间",
      key: "rest_start",
      dayAfterKey: 'rest_start_after',
      type: "date-time",
      index: [0, 0, 0],
      range: generateDatetimeRange()
    },
    {
      label: "休息结束时间",
      key: "rest_end",
      type: "date-time",
      dayAfterKey: 'rest_end_after',
      index: [0, 0, 0],
      range: generateDatetimeRange()
    }
  ];

  const formConfigMap = {
    work: getWorkForm,
    "off-work": getOffWorkForm,
    rest: getRestForm
  }

  const formConfig = ref<FormItemConfig[]>(formConfigMap[props.type]());

  watch(
    allowNextDay,
    () => {
      if (props.allowNextDay !== undefined) {
        for (const item of formConfig.value) {
          if (item.type === "date-time") {
            item.range = generateDatetimeRange(!props.allowNextDay);
          }
        }
      }
    },
    {
      immediate: true
    }
  );

  const emit = defineEmits(['change']);

  const handlePickerChange = (item : FormItemConfig, e : any) => {
    const nextValue = {};

    if (item.type === 'date-time') {
      if (item.dayAfterKey) {
        Object.assign(nextValue, {
          [item.dayAfterKey]: e.detail.value[0]
        });
      }

      const hour = item.range[1][e.detail.value[1]];
      const minute = item.range[2][e.detail.value[2]];

      Object.assign(nextValue, {
        [item.key]: `${hour}:${minute}`
      });
    } else if (item.type === 'hour-time') {
      Object.assign(nextValue, {
        [item.key]: e.detail.value[0] * 3600 + e.detail.value[1] * 60
      });
    }
    emit('change', props.prefix, nextValue);
  };

  const handleSwitchChange = (item : FormItemConfig, e : any) => {
    emit('change', props.prefix, {
      [item.key]: e.detail.value
    });
  }

  // 监听外部表单信息变化，同步外部表单信息到内部状态
  watch(
    form,
    (newVal) => {
      const dayAfterKeyMap = {
        work: "first_day_after",
        "off-work": "second_day_after",
        rest: "rest_day_after"
      };
      for (const item of formConfig.value) {
        if (item.type === "date-time") {
          const instance = moment(newVal[item.key], "HH:mm");
          const nextIndex = [
            newVal[dayAfterKeyMap[props.type]],
            instance.hour(),
            instance.minute()
          ];
          item.index = nextIndex;
        } else if (item.type === "hour-time") {
          const duration = moment.duration(newVal[item.key], 'seconds');

          // 提取小时和分钟
          const hours = duration.hours();
          const minutes = duration.minutes();
          item.index = [hours, minutes];
        } else if (item.type === "switch") {
          item.checked = newVal[item.key];
        }
      }
    },
    {
      immediate: true,
      deep: true
    });
</script>

<style scoped lang="scss">
  .form-body {
    background: #F5F6F7;
    border-radius: 12rpx;
    padding-left: 24rpx;


  }

  .form-item {
    height: 102rpx;
    display: flex;
    align-items: center;


    &+& {
      border-top: 1px solid #EEEEEE;
    }

    .form-item-content {
      flex: 1;
      margin-left: 24rpx;
    }

    .switch-content,
    .picker-content {
      display: flex;
      justify-content: flex-end;
      padding-right: 16rpx;

      .iconfont {
        color: #C0C4CC;
        font-size: 14px;
        margin-left: 8rpx;
      }
    }

    .switch-content switch {
      transform: scale(0.75);
    }
  }
</style>