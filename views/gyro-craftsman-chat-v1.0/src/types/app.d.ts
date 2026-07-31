export interface App {
  id: number;
  name: string;
  pic: string;
  info: string;
  prologue_list: string[];
  prologue_text: string;
}

export type AppPreviewCache = {
  appId: number; // 应用ID
  prologueText: string; // 应用提示词
  prologueList: string[]; // 应用提示词列表
};

export type UpdateAppPreviewData = {
  appId: number; // 应用ID
  prologueText: string; // 应用提示词
  prologueList: string[]; // 应用提示词列表
};
