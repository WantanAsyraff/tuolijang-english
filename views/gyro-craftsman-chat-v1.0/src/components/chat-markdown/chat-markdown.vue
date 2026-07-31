<script lang="ts">
import { renderMd, unifiedMd } from "./chat-markdown-render";

export default defineComponent({
  props: {
    content: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const { content } = toRefs(props);

    // 解析 markdown 语法树
    const markdownAst = computed(() => {
      return unifiedMd.parse(content.value);
    });

    // 渲染 markdown 语法树
    return () => renderMd(markdownAst.value);
  }
});
</script>

<style scoped lang="scss"></style>
