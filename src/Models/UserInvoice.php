<?php

namespace System\Models;

class UserInvoice extends Model
{
    public const INVOICE_OPEN_TYPE_PERSONAL = 1;  // 个人
    public const INVOICE_OPEN_TYPE_COMPANY = 2;  // 企业
    public const INVOICE_OPEN_TYPE = [
        self::INVOICE_OPEN_TYPE_PERSONAL => '个人',
        self::INVOICE_OPEN_TYPE_COMPANY => '企业',
    ];

    public const INVOICE_TYPE_ORDINARY = 1;  // 增值税普通发票
    public const INVOICE_TYPE_SPECIAL = 2;  // 增值税专用发票
    public const INVOICE_TYPE_ORGANIZATION = 3;  // 组织（非企业）增值税普通发票
    public const INVOICE_TYPE = [
        self::INVOICE_TYPE_ORDINARY => '增值税普通发票',
        self::INVOICE_TYPE_SPECIAL => '增值税专用发票',
        self::INVOICE_TYPE_ORGANIZATION => '组织（非企业）增值税普通发票',
    ];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'open_type_format', 'invoice_type_format',
    ];

    public function getOpenTypeFormatAttribute()
    {
        if (isset(self::INVOICE_OPEN_TYPE[$this->open_type])) {
            return self::INVOICE_OPEN_TYPE[$this->open_type];
        } else {
            return '未知';
        }
    }

    public function getInvoiceTypeFormatAttribute()
    {
        if (isset(self::INVOICE_TYPE[$this->invoice_type])) {
            return self::INVOICE_TYPE[$this->invoice_type];
        } else {
            return '未知';
        }
    }
}
