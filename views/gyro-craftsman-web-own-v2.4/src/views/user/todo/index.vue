<template>
  <div class="divBox">
    <my-todo ref="todoRef" />
  </div>
</template>
<script>
import MyTodo from '@/views/user/calendar/components/MyTodo.vue'
export default {
  components: {
    MyTodo
  },
  data() {
    return {
      list: []
    }
  }
}
</script>
<style lang="scss" scoped></style>
