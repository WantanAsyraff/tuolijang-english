

export const CUSTOMER_MODULE_KEYS = {
    LEADS:       'lead_module_switch',
    CUSTOMER:    'customer_module_switch',
    OPPORTUNITY: 'opportunity_module_switch',
    CONTRACT:    'contract_module_switch',
    ORDER:       'order_module_switch',
    INVOICE:     'invoice_module_switch',
    LIAISON:     'liaison_module_switch',
}

// 客户模块与组件路径的映射关系
export const CUSTOMER_MODULE_PATHS = {
    [CUSTOMER_MODULE_KEYS.LEADS]: '/pages/customer/lead/index',
    [CUSTOMER_MODULE_KEYS.CUSTOMER]: '/pages/customer/list/index',
    [CUSTOMER_MODULE_KEYS.OPPORTUNITY]: '/pages/customer/opportunity/index',
    [CUSTOMER_MODULE_KEYS.CONTRACT]: '/pages/customer/signing/index',
    [CUSTOMER_MODULE_KEYS.ORDER]: '/pages/customer/contract/index',
    [CUSTOMER_MODULE_KEYS.INVOICE]: '/pages/customer/invoice/index',
    [CUSTOMER_MODULE_KEYS.LIAISON]: '/pages/customer/list/addLiaison',
}

export const API_KEY_TO_MODULE_KEY = {
    lead_module_switch: CUSTOMER_MODULE_KEYS.LEADS,
    customer_module_switch: CUSTOMER_MODULE_KEYS.CUSTOMER,
    opportunity_module_switch: CUSTOMER_MODULE_KEYS.OPPORTUNITY,
    contract_module_switch: CUSTOMER_MODULE_KEYS.CONTRACT,
    order_module_switch: CUSTOMER_MODULE_KEYS.ORDER,
    invoice_module_switch: CUSTOMER_MODULE_KEYS.INVOICE,
    liaison_module_switch: CUSTOMER_MODULE_KEYS.LIAISON,
  };


// 按钮权限配置 - 按钮名称与模块key的映射关系
export const BUTTON_MODULE_MAPPING: Record<string, string> = {
    '添加商机': CUSTOMER_MODULE_KEYS.OPPORTUNITY,
    '添加合同': CUSTOMER_MODULE_KEYS.CONTRACT,
    '添加订单': CUSTOMER_MODULE_KEYS.ORDER,
    '申请发票': CUSTOMER_MODULE_KEYS.INVOICE,
    '添加联系人': CUSTOMER_MODULE_KEYS.LIAISON,
}

// 菜单权限配置 - 菜单名称与模块key的映射关系
export const MENU_MODULE_MAPPING: Record<string, string> = {
    '线索': CUSTOMER_MODULE_KEYS.LEADS,
    '客户': CUSTOMER_MODULE_KEYS.CUSTOMER,
    '商机': CUSTOMER_MODULE_KEYS.OPPORTUNITY,
    '合同': CUSTOMER_MODULE_KEYS.CONTRACT,
    '订单': CUSTOMER_MODULE_KEYS.ORDER,
    '发票': CUSTOMER_MODULE_KEYS.INVOICE,
    '联系人': CUSTOMER_MODULE_KEYS.LIAISON,
}

// 菜单路径到模块key的映射关系
export const MENU_PATH_MODULE_MAPPING: Record<string, string> = {
    '/pages/customer/lead/index': CUSTOMER_MODULE_KEYS.LEADS,
    '/pages/customer/list/index': CUSTOMER_MODULE_KEYS.CUSTOMER,
    '/pages/customer/opportunity/index': CUSTOMER_MODULE_KEYS.OPPORTUNITY,
    '/pages/customer/signing/index': CUSTOMER_MODULE_KEYS.CONTRACT,
    '/pages/customer/contract/index': CUSTOMER_MODULE_KEYS.ORDER,
    '/pages/customer/invoice/index': CUSTOMER_MODULE_KEYS.INVOICE,
    '/pages/customer/list/addLiaison': CUSTOMER_MODULE_KEYS.LIAISON,
}

// 获取客户模块开关值
export const getCustomerSwitch = (): Record<string, number> => {
    const customerSwitchValue = JSON.parse(uni.getStorageSync("storageUserData")).enterprise.customer_switch;
    return customerSwitchValue;
}

// 根据模块key检查是否有权限（返回 true 表示有权限）
export const isModuleEnabled = (moduleKey: string): boolean => {
    const customerSwitch = getCustomerSwitch();
    // 如果没有配置开关值，默认有权限
    if (!customerSwitch) {
        return true;
    }
    // 检查开关值，1 表示启用，0 表示禁用
    const switchValue = customerSwitch[moduleKey];
    return switchValue === 1 || switchValue === true;
}

// 根据路径检查菜单是否有权限
const isMenuPathEnabled = (path: string): boolean => {
    const moduleKey = MENU_PATH_MODULE_MAPPING[path];
    // 如果路径不在权限配置中，默认有权限
    if (!moduleKey) {
        return true;
    }
    return isModuleEnabled(moduleKey);
}

// 根据按钮名称检查是否有权限（返回 true 表示有权限）
export const checkButtonPermission = (buttonName: string): boolean => {
    const moduleKey = BUTTON_MODULE_MAPPING[buttonName];
    // 如果按钮不在权限配置中，默认有权限
    if (!moduleKey) {
        return true;
    }
    return isModuleEnabled(moduleKey);
}

// 过滤有权限的按钮列表
export const filterPermissionButtons = <T extends { name: string }>(buttons: T[]): T[] => {
    const customerSwitch = getCustomerSwitch();
    // 如果没有配置开关值，返回所有按钮
    if (!customerSwitch) {
        return buttons;
    }
    return buttons.filter(button => {
        const moduleKey = BUTTON_MODULE_MAPPING[button.name];
        // 如果按钮不在权限配置中，默认有权限
        if (!moduleKey) {
            return true;
        }
        return isModuleEnabled(moduleKey);
    });
}

// 过滤有权限的菜单列表（根据uni_path路径判断）
export const filterPermissionMenus = <T extends { menu_name?: string; children?: T[]; uni_path?: string }>(menus: T[]): T[] => {
    const customerSwitch = getCustomerSwitch();
    // 如果没有配置开关值，返回所有菜单
    if (!customerSwitch) {
        return menus;
    }
    return menus.filter(menu => {
        // 检查当前菜单的 uni_path 是否有权限
        const isEnabled = menu.uni_path ? isMenuPathEnabled(menu.uni_path) : true;
        // 如果当前菜单被禁用，且有 children，则过滤 children
        if (menu.children && menu.children.length > 0) {
            menu.children = filterPermissionMenus(menu.children);
        }
        // 如果当前菜单被禁用且没有子菜单或子菜单被过滤后为空，则不显示
        if (!isEnabled && (!menu.children || menu.children.length === 0)) {
            return false;
        }
        return true;
    });
}
// 过滤首页快捷入口的菜单列表（根据uni_path路径判断）
// 根据 uni_path 过滤有权限的菜单
export const filterQuickMenus = <T extends { uni_path?: string }>(menus: T[]): T[] => {
    const customerSwitch = getCustomerSwitch();
    if (!customerSwitch) {
        return menus;
    }
    return menus.filter(menu => {
        const moduleKey = menu.uni_url ? MENU_PATH_MODULE_MAPPING[menu.uni_url] : null;
        if (!moduleKey) return true;
        return isModuleEnabled(moduleKey);
    });
}

// 根据模块key检查模块是否启用
export const isModuleSwitchEnabled = (moduleKey: string): boolean => {
    return isModuleEnabled(moduleKey);
}
