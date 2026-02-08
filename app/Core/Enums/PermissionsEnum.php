<?php

namespace App\Core\Enums;

enum PermissionsEnum: string
{
    // Company management permissions
    case MANAGE_COMPANIES = 'companies.list';
    case CREATE_COMPANY = 'companies.create';
    case VIEW_COMPANY = 'companies.view';
    case EDIT_COMPANY = 'companies.edit';
    case DELETE_COMPANY = 'companies.delete';
    case RESTORE_COMPANY = 'companies.restore';
    case FORCE_DELETE_COMPANY = 'companies.forceDelete';

    // Company Group management permissions
    case MANAGE_GROUPS = 'company.groups.manage';
    case LIST_GROUPS = 'company.groups.list';
    case CREATE_GROUPS = 'company.groups.create';
    case VIEW_GROUPS = 'company.groups.view';
    case EDIT_GROUPS = 'company.groups.edit';
    case DELETE_GROUPS = 'company.groups.delete';

    // Price list management permissions
    case MANAGE_PRICE_LISTS = 'company.price.lists.manage';
    case CREATE_PRICE_LIST = 'company.price.lists.list';
    case VIEW_PRICE_LIST = 'company.price.lists.view';
    case EDIT_PRICE_LIST = 'company.price.lists.edit';
    case DELETE_PRICE_LIST = 'company.price.lists.delete';
    case MANAGE_PRODUCT_PRICE_HISTORIES = 'company.price.history.manage';
    case MANAGE_PENDING_PRICE_CHANGES = 'company.price.pending.change.manage';

    // Supplier management permissions
    case MANAGE_PROVIDER = 'providers.list';
    case CREATE_PROVIDER = 'providers.create';
    case VIEW_PROVIDER = 'providers.view';
    case EDIT_PROVIDER = 'providers.edit';
    case DELETE_PROVIDER = 'providers.delete';

    // Product management permissions
    case MANAGE_PRODUCTS = 'products.manage';
    case LIST_PRODUCTS = 'products.list';
    case CREATE_PRODUCT = 'products.create';
    case VIEW_PRODUCT = 'products.view';
    case EDIT_PRODUCT = 'products.edit';
    case DELETE_PRODUCT = 'products.delete';
    case FORCE_DELETE_PRODUCT = 'products.forceDelete';
    case LIST_IMPORTS = 'imports.list';
    case VIEW_IMPORTS = 'imports.view';
    case PROCESS_IMPORTS = 'imports.process';
    case DELETE_IMPORTS = 'imports.delete';
    case IMPORT_PRODUCTS = 'imports.products';
    case MANAGE_EXPORTS = 'exports.list';
    case EXPORT_PRODUCTS = 'exports.products';

    // User management permissions
    case MANAGE_USERS = 'users.manage';
    case LIST_USERS = 'users.list';
    case CREATE_USER = 'users.create';
    case VIEW_USER = 'users.view';
    case EDIT_USER = 'users.edit';
    case DELETE_USER = 'users.delete';
    case RESTORE_USER = 'users.restore';
    case CHANGE_USER_PASSWORD = 'users.changePassword';
    case FORCE_DELETE_USER = 'users.forceDelete';

    // Role and permission management
    case MANAGE_ROLES = 'roles.manage';
    case LIST_ROLES = 'roles.list';
    case ASSIGN_ROLES = 'roles.assign';
    case VIEW_ROLES = 'roles.view';
    case MANAGE_PERMISSIONS = 'permissions.manage';
    case LIST_PERMISSIONS = 'permissions.list';
    case ASSIGN_PERMISSIONS = 'permissions.assign';
    case VIEW_PERMISSIONS = 'permissions.view';

    // Settings
    case MANAGE_SETTINGS = 'settings.manage';
}
