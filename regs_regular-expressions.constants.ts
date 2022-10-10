
export const PHONE_NUMBER_REGEX = /^\d*$/;
export const ADDRESS_NUMBER_REGEX = /^[0-9]+[\/0-9a-zA-Zа-яґєіїА-ЯЄҐЇІ]*$/;
export const DEFAULT_NUMBER_REGEX = /^[\d]*$/;
export const DEFAULT_FLOAT_REGEX = /^[\d]+[.,]?[\d]*$/;
export const ITN_REGEX = /(?<!\d)[\d]{2,50}(?!\d-)/;
export const COMPANY_NAME_REGEX = /^([а-яґєіїА-ЯЄҐЇІ\w]{2,})+(?: [а-яґєіїА-ЯЄҐЇІ\w]{2,})*$/;
export const SUBDOMAIN_REGEX = /^[^-_A-Z][a-z0-9_-]*[^-_A-Z]$/;
export const NOSPACES_REGEX = /^\S$|^\S[\s\S]*\S$/;
export const NOT_ZERO_FIRST = /^[1-9][0-9]*$/;

