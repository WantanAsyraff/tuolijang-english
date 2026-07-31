<template>
  <!-- 最近对话 -->
  <div class="flex-1 relative">
    <div class="absolute inset-0 py-15px px-14px flex flex-col">
      <div class="text-14px leading-26px font-bold flex items-center justify-between">
        {{ t("common.recentConversations") }}
        <i class="ai-icon ai-icon-jinru text-14px leading-26px color-#C2C3C5 transition cursor-pointer chat-arrow-icon"
          :class="{ 'is-collapse': isCollapse }" @click="handleToggleCollapse" />
      </div>

      <!-- 最近对话列表 -->
      <Transition name="collapse">
        <ul class="flex-1 overflow-y-auto mt-15px" v-if="!isCollapse" @mouseover="handleCollapseMouseOver"
          @click="handleChatListClick">
          <li v-for="(chat, index) of chatList" :key="chat.id" class="relative chat-item px-10px rounded-6px"
            :class="{ active: chat.id === currentChatId }" :data-chat-index="index">
            <router-link :to="{ name: ROUTE_KEY.CHAT_MAIN, params: { id: chat.id } }"
              class="h-34px flex items-center text-12px leading-17px color-#606266 cursor-pointer transition chat-item-link">
              <span class="single-line flex-1">{{ chat.title }}</span>
            </router-link>

            <!-- 下拉菜单 -->
            <el-dropdown trigger="click" placement="bottom-start" popper-class="chat-dropdown-popper"
              @command="handleDropdownCommand" v-if="dropdownVisibleIdxSet.has(index)">
              <div>
                <i class="ai-icon ai-icon-gengduo3 text-14px leading-16px color-#606266" />
              </div>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item v-for="item in dropdownMenuConfig" :key="item.command" :command="item.command"
                    :data-id="chat.id">
                    {{ item.getLabel ? item.getLabel(chat) : item.label }}
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>

            <!-- 重命名 -->
            <div class="absolute inset-0" v-if="currentEditChatId === chat.id">
              <el-input v-focus class="absolute inset-0 rename-input" v-model="currentEditChatTitle"
                @blur="handleRenameInputConfirm" @change="handleRenameInputConfirm"
                @keyup.enter="handleRenameInputConfirm" />
            </div>
          </li>
        </ul>
      </Transition>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import { useCollapse } from "./composables/useCollapse";
import { useSidebarStore } from "@/pinia/stores/ui/useSidebarStore";
import { useChatStore } from "@/pinia/stores/useChatStore";
import { storeToRefs } from "pinia";
import { getDataSet } from "@/utils/helper";
import { ROUTE_KEY } from "@/constants/route-key";
import type { Chat } from "@/types/chat";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { Message } from "@/utils/message";
import { handleError } from "@/utils/error-handler";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const { handleCloseSidebar } = useSidebarStore();
const route = useRoute();
const router = useRouter();
const chatStore = useChatStore();
const rootStore = useRootStore();

const { chatList } = storeToRefs(chatStore);
const { currentChatId } = storeToRefs(rootStore);

const currentEditChatId = ref<number | null>(null);
const currentEditChatTitle = ref<string>("");

enum DropdownMenuCommand {
  Rename = "rename",
  SetTop = "set-top",
  Delete = "delete"
}

const dropdownMenuConfig = computed(() => [
  {
    label: t("common.rename"),
    command: DropdownMenuCommand.Rename
  },
  {
    getLabel: (chat: Readonly<Chat>) => chat.isTopUp ? t("common.unpin") : t("common.pin"),
    command: DropdownMenuCommand.SetTop
  },
  {
    label: t("common.delete"),
    command: DropdownMenuCommand.Delete
  }
]);

const { isCollapse, dropdownVisibleIdxSet, handleToggleCollapse, handleCollapseMouseOver } = useCollapse();

const handleConfirmDeleteChat = async (chatId: number) => {
  try {
    await ElMessageBox.confirm(t("chat.deleteConversationConfirm"), t("common.tips"), {
      confirmButtonText: t("common.confirm"),
      cancelButtonText: t("common.cancel"),
      type: "error"
    });
  } catch {
    return;
  }

  try {
    await chatStore.deleteChat(chatId);
    Message.success(t("common.deleteSuccess"));
    if (route.name === ROUTE_KEY.CHAT_MAIN && currentChatId.value === chatId) {
      router.replace({ name: ROUTE_KEY.CHAT_INDEX });
    }
  } catch (error: any) {
    handleError(error);
  }
};

let showInputTime: number | null = null;

const handleDropdownCommand = (command: DropdownMenuCommand, _: any, event: MouseEvent) => {
  const chatId = getDataSet(event.target as HTMLElement, "", "id", false);

  if (!chatId) return;
  const chatIdNumber = Number(chatId);
  const chat = chatList.value.find(item => item.id === chatIdNumber);

  switch (command) {
    case DropdownMenuCommand.Rename:
      showInputTime = Date.now();
      currentEditChatId.value = chatIdNumber;
      if (chat) {
        currentEditChatTitle.value = chat.title;
      }
      break;
    case DropdownMenuCommand.SetTop:
      if (!chat) return;
      const isTopUp = chat.isTopUp;
      chatStore.setTopUpChat(chatIdNumber, isTopUp)
        .then(() => {
          Message.success(isTopUp ? t("common.unpinSuccess") : t("common.pinSuccess"));
        })
        .catch(handleError);
      break;
    case DropdownMenuCommand.Delete:
      handleConfirmDeleteChat(chatIdNumber);
      break;
  }
};

const handleChatListClick = (e: MouseEvent) => {
  const target = e.target;
  if (!(target instanceof HTMLElement)) return;

  // a 标签内部点击时关闭 sidebar，only mobile
  if (target.closest(".chat-item-link")) {
    handleCloseSidebar();
  }
};

const handleRenameInputConfirm = () => {
  const now = Date.now();
  const diff = now - (showInputTime ?? 0);
  if (diff < 100) return;

  const title = currentEditChatTitle.value.trim();
  if (title && currentEditChatId.value) {
    chatStore.renameChat(currentEditChatId.value, title)
      .then(() => {
        // Message.success("重命名成功");
      })
      .catch(handleError);
  }

  currentEditChatId.value = null;
  currentEditChatTitle.value = "";
};

const vFocus = {
  async mounted(el: HTMLElement) {
    setTimeout(() => {
      el.querySelector("input")?.focus();
    }, 100);
  }
};

</script>

<style scoped lang="scss">
.chat-arrow-icon {
  transform: rotate(90deg);

  &.is-collapse {
    transform: rotate(0deg);
  }
}

.rename-input {
  --el-font-size-base: 12px;
}

.chat-item {
  .el-dropdown {
    position: absolute;
    right: 10px;
    top: 8.6px;
    visibility: hidden;
  }

  &+.chat-item {
    margin-top: 1px;
  }

  &:hover,
  &.active {
    background-color: #F3F4F5;
  }

  &:hover {
    @apply pr-37px;

    .el-dropdown {
      visibility: visible;
    }
  }
}

:global(.chat-dropdown-popper .el-popper__arrow) {
  display: none;
}

:global(.chat-dropdown-popper) {
  --el-text-color-regular: initial;
  --el-dropdown-menuItem-hover-color: initial;
  --el-dropdown-menuItem-hover-fill: #F2F3F5;
}

:global(.chat-dropdown-popper .el-dropdown-menu) {
  width: 84px;
}

.collapse-enter-active,
.collapse-leave-active {
  transition: clip-path .3s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapse-enter-to,
.collapse-leave-from {
  clip-path: inset(0 0 0 0);
}

.collapse-enter-from,
.collapse-leave-to {
  clip-path: inset(0 0 100% 0);
}
</style>
