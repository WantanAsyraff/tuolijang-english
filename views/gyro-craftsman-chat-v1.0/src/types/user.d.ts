export interface UserInfo {
  id: number;
  uid: string;
  account: string;
  avatar: string;
  name: string;
  phone: string;
}

export interface EnterpriseInfo {
  title: string;
  enterprise_name: string;
  enterprise_name_en: string;
  entid: number;
  logo: string;
  uniqued: string;
  maxScore: number;
  culture: string;
  compute_mode: number;
}
