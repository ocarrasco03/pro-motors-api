<?php

namespace App\Core\Enums;

enum PermissionsEnum:string
{
    // Company management permissions
    case MANAGE_COMPANIES = "companies.list";
    case CREATE_COMPANY = "companies.create";
    case VIEW_COMPANY = "companies.view";
    case EDIT_COMPANY = "companies.edit";
    case DELETE_COMPANY = "companies.delete";
    case RESTORE_COMPANY = "companies.restore";
    case FORCE_DELETE_COMPANY = "companies.forceDelete";

    // Price list management permissions
    case CREATE_PRICE_LIST = 'create price list';
    case VIEW_PRICE_LIST = 'view price list';
    case EDIT_PRICE_LIST = 'edit price list';
    case DELETE_PRICE_LIST = 'delete price list';

    // Group management permissions
    case MANAGE_GROUPS = "manage groups";
    case CREATE_GROUPS = "create group";
    case VIEW_GROUPS = 'view group';
    case EDIT_GROUPS = 'edit group';
    case DELETE_GROUPS = 'delete group';

    // Supplier management permissions
    case MANAGE_SUPPLIERS = 'manage suppliers';
    case CREATE_SUPPLIER = 'create supplier';
    case VIEW_SUPPLIER = 'view supplier';
    case EDIT_SUPPLIER = 'edit supplier';
    case DELETE_SUPPLIER = 'delete supplier';

    // Product management permissions
    case MANAGE_PRODUCTS = 'manage products';
    case CREATE_PRODUCT = 'create product';
    case VIEW_PRODUCT = 'view product';
    case EDIT_PRODUCT = 'edit product';
    case VIEW_IMPORTS = 'view imports';
    case PROCESS_IMPORTS = 'process imports';
    case DELETE_IMPORTS = 'delete imports';
    case DELETE_PRODUCT = 'delete product';
    case IMPORT_PRODUCTS = 'import products';
    case EXPORT_PRODUCTS = 'export products';

    // User management permissions
    case MANAGE_USERS = 'manage users';
    case CREATE_USER = 'create user';
    case VIEW_USER = 'view user';
    case EDIT_USER = 'edit user';
    case DELETE_USER = 'delete user';
    case CHANGE_USER_PASSWORD = 'change user password';

    // Profile permissions
    case VIEW_PROFILE = 'view profile';
    case EDIT_PROFILE = 'edit profile';
    case CHANGE_PROFILE_PASSWORD = 'change profile password';

    // Role and permission management
    case MANAGE_ROLES = 'manage roles';
    case ASSIGN_ROLES = 'assign roles';
    case VIEW_ROLES = 'view roles';
    case DELETE_ROLES = 'delete roles';
    case MANAGE_PERMISSIONS = 'manage permissions';
    case ASSIGN_PERMISSIONS = 'assign permissions';
    case VIEW_PERMISSIONS = 'view permissions';
    case DELETE_PERMISSIONS = 'delete permissions';
}
