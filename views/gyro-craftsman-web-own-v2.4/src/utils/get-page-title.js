import { $ } from '@/lang'
import defaultSettings from '@/settings';
const title = defaultSettings.title || 'OA系统企业端';

export default function getPageTitle(key) {
  const routeKey = `route.${key}`;
  const pageName = $(routeKey);
  if (pageName !== routeKey) {
    return `${pageName} - ${$(defaultSettings.title || title)}`;
  }
  return `${$(defaultSettings.title || title)}`;
}
