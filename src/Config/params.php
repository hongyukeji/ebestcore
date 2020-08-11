<?php

return [
    'common' => [],
    'backend' => [
        'view' => [
            // 搜索条件框状态: 1=>展开,0=>折叠
            'search_box_status' => true,
        ],
    ],
    'frontend' => [],
    'mobile' => [],
    'regex' => [
        // ^1 以1开头，\d表示数字，\d{10}表示数字出现10次，加上前面以1开头，正好是11个数字，X$表示以X结尾，这里用$表示后面没有了，11个数字后已经是匹配字符串的结尾。
        'mobile' => '/^1\d{10}$/',
        // 用户名验证规则：用户名只能由数字、字母、中文汉字及横杠、下划线组成，不能包含特殊符号。
        'username' => '/^[A-Za-z0-9_-\x{4e00}-\x{9fa5}]+$/u',
        // 中文、英文、数字包括下划线
        'username_simple' => '/^[\u4E00-\u9FA5A-Za-z0-9_]+$/',
        // 由26个英文字母组成的字符串
        'english_simple' => '/^[A-Za-z]+$/',
        'english_number' => '/^[A-Za-z0-9]+$/',
        'english_number_symbol' => '/^\w+$ 或 ^\w{3,20}$/',
        // 价格匹配正则表达式：大于0，且不能为负数 /^(?!0\d|[0.]+$)\d{1,8}(\.\d{1,2})?$/
        'price' => '/^[0-9]{1,10}+(.[0-9]{1,2})?$/',
        'url' => '/^http(s)?:\\/\\/.+/',
    ],
    'cache' => [
        'commons' => [
            'short_url' => [
                'prefix' => 'common_short_url_',
                'expires_at' => 0,
            ],
        ],
        'backend' => [
            'verify_code' => [
                'prefix' => 'backend_verify_code_',
                'expires_at' => 60 * 15,
            ],
        ],
    ],
    'orders' => [
        'payment_ttl' => 60 * 60 * 24,  // 订单支付时间
        'confirm_receipt_ttl' => 60 * 60 * 24 * 10, // 订单确认收货时间
    ],
    'auth' => [
        'login_field' => [
            'name',
            'mobile',
            'email'
        ],
        'login_name' => 'username',
        'user_register' => true,
        'admin_register' => false,
    ],
    'users' => [
        'default_avatar' => '/assets/inspinia/img/landing/user7-160x160.jpg',
        'user_default_avatar' => '/assets/frontend/static/img/public/user/default_avatar.jpg',
        'default_avatar_random' => true,
        'max_address_number' => 5,
        'max_invoice_number' => 5,
        'avatars' => [
            '/assets/inspinia/img/landing/user7-160x160.jpg',
            '/assets/inspinia/img/landing/user1-128x128.jpg',
            '/assets/inspinia/img/landing/user2-160x160.jpg',
            '/assets/inspinia/img/landing/user3-200x200.jpg',
            '/assets/inspinia/img/a1.jpg',
            '/assets/inspinia/img/a2.jpg',
            '/assets/inspinia/img/a3.jpg',
            '/assets/inspinia/img/a4.jpg',
            '/assets/inspinia/img/a5.jpg',
            '/assets/inspinia/img/a6.jpg',
            '/assets/inspinia/img/a7.jpg',
            '/assets/inspinia/img/a8.jpg',
            '/assets/inspinia/img/landing/avatar1.jpg',
            '/assets/inspinia/img/landing/avatar2.jpg',
            '/assets/inspinia/img/landing/avatar3.jpg',
            '/assets/inspinia/img/landing/avatar4.jpg',
            '/assets/inspinia/img/landing/avatar5.jpg',
            '/assets/inspinia/img/landing/avatar6.jpg',
            '/assets/inspinia/img/landing/avatar7.jpg',
            '/assets/inspinia/img/landing/avatar8.jpg',
            '/assets/inspinia/img/landing/avatar9.jpg',
        ],
    ],
    'products' => [
        'default_image' => '/assets/frontend/static/img/public/goods/default_image.png',
        'product_code' => [
            'status' => true,
            'prefix' => 'WMT',
            'length' => '16',
            'pad_string' => '0',
        ],
        'default' => [
            'stock_count_mode' => '1',
            'audit_status' => '1',
            'status' => '1',
        ],
    ],
    'shops' => [
        'default_image' => '/assets/frontend/static/img/public/goods/default_image.png',
        'default_name' => '默认店铺',
    ],
    'paths' => [
        'env_file' => base_path('.env'),
    ],
    'icons' => [
        'default_icon' => 'fa fa-circle-o',
    ],
    'sms' => [
        'default' => [
            'verify_code' => '1234'
        ],
    ],
    'sites' => [
        'delimiter' => '-',
    ],
    'pages' => [
        'per_page' => '15',
        'per_pages' => [10, 15, 20, 50, 100, 500, 1000, 2000]
    ],
    'models' => [
        'sort_default' => '1000',
        'sort_key' => 'sort',
        'sort_mode' => 'desc',
        'status_inactive' => '0',
        'status_active' => '1',
    ],
    'statistics' => [],
    'distribution' => [
        'status' => false,  // 分销状态
        'calc_method' => 1, // 分销金额计算方式:1-商品价格百分比(%),2-固定金额
        'rebate_level', 2, // 返利层级
    ],
    'register' => [
        'status' => 1,
        // register_columns: name,password,password_confirmation,email,mobile,mobile_verify_code,captcha,agreement
        'register_columns' => [
            'name',
            'password',
            'mobile',
            'mobile_verify_code',
            'captcha',
            'agreement',
        ],
        'censor_names' => 'admin,管理,官方',
        'register_agreement' => '<p style="text-align:center"><strong>旺迈特用户注册协议</strong></p>

<p>　　尊敬的用户您好，欢迎您访问旺迈特网站（以下简称：网站）。在您注册成为网站会员之前，请您务必认真阅读和理解《注册协议》（以下简称：协议）中所有的条款。您须完全同意协议中所有的条款，才可以注册成为本网站的会员，使用里面的服务。您在网站的注册和操作均将被视为是您对协议所有条款及内容的自愿接受。<br />
<strong>　　第一条</strong> <strong>声明</strong><br />
　　（一）网站内在线产品的所有权归旺迈特所有。<br />
　　（二）您在网站进行注册时，勾选&ldquo;阅读并同意《注册协议》 &rdquo;按钮，即表示为您已自愿接受协议中所有的条款和内容。<br />
　　（三）协议条款的效力范围仅限于本网站，您在网站的行为均受协议的约束。<br />
　　（四）您使用网站服务的行为，即被视为您已知悉本网站的相关公告并同意。<br />
　　（五）本网站有权在未提前通知您的情况下修改协议的条款，您每次进入网站在使用服务前，都应先查阅一遍协议。<br />
　　（六）本网站有权在未提前通知您的情况下修改、暂停网站部分或全部的服务，且不承担由此产生来自您和第三方的任何责任。<br />
　　（七）本网站提供免费注册服务，您的注册均是自愿行为，注册成功后，您可以得到网站更加完善的服务。<br />
　　（八）您注册成为会员后账户和密码如有灭失，不会影响到您已办理成功业务的效力，本网站可恢复您的注册账户及相关信息但不承担除此以外的其它任何责任。<br />
<strong>　　第二条 用户管理</strong><br />
　　（一）您在本网站的所有行为都须符合中国的法律法规，您不得利用本网站提供的服务制作、复制、发布、传播以下信息：<br />
　　1、反对宪法基本原则的；<br />
　　2、危害国家安全、泄露国家秘密、颠覆国家政权、破坏国家统一的；<br />
　　3、损害国家荣誉和利益的；<br />
　　4、煽动民族仇恨、民族歧视、破坏民族团结的；<br />
　　5、破坏国家宗教政策，宣扬邪教和封建迷信的；<br />
　　6、散布谣言、扰乱社会秩序、破坏社会稳定的；<br />
　　7、散布淫秽、色情、赌博、暴力、凶杀、恐怖内容或者教唆犯罪的；<br />
　　8、侮辱或者诽谤他人，侵害他人合法权益的；<br />
　　9、以及法律、法规禁止的其他内容；<br />
　　（二）您在本网站的行为，还必须符合其它国家和地区的法律规定以及国际法的有关规定。<br />
　　（三）不得利用本网站从事以下活动：<br />
　　1、未经允许，进入他人计算机信息网络或者使用他人计算机信息网络的资源；<br />
　　2、未经允许，对他人计算机信息网络的功能进行删除、修改或增加；<br />
　　3、未经允许，对他人计算机信息网络中存储、处理或者传输的数据和应用程序进行删除、修改或者增加；<br />
　　4、制作、故意传播计算机病毒等破坏性程序的；<br />
　　5、其他危害计算机信息网络安全的行为；<br />
　　（四）遵守本网站其他规定和程序：<br />
　　1、您对自己在本网站中的行为和操作承担全部责任；<br />
　　2、您承担责任的形式包括但不仅限于，对受到侵害者进行赔偿、在本网站首先承担了因您的行为导致的行政处罚或侵权损害赔偿责任后，您应给予本网站的等额赔偿；<br />
　　3、如果本网站发现您传输的信息含有本协议<u>第二条所列内容之一的</u>，本网站有权在不通知您的情况下采取包括但不仅限于向国家有关机关报告、保存有关记录、删除该内容及链接地址、关闭服务器、暂停您账号的操作权限、停止向您提供服务等措施；<br />
<strong>　　第三条 注册会员权利和义务</strong><br />
　　（一）注册会员有权用本网站提供的服务功能。<br />
　　（二）注册会员同意遵守包括但不仅限于《中华人民共和国保守国家秘密法》、《中华人民共和国计算机信息系统安全保护条例》、《计算机软件保护条例》、《互联网电子公告服务管理规定》、《互联网信息服务管理办法》等在内的法律、法规。<br />
　　（三）您注册时有义务提供完整、真实、的个人信息，信息如有变更，应及时更新。<br />
　　（四）您成为注册会员须妥善保管用户名和密码，用户登录后进行的一切活动均视为是您本人的行为和意愿，您负全部责任。<br />
　　（五）您在使用本网站服务时，同意且接受本网站提供的各类信息服务。<br />
　　（六）您使用本网站时，禁止有以下行为：<br />
　　1、上载、张贴、发送电子邮件或以其他方式传送含有违反国家法律、法规的信息或资料，这些资料包括但不仅限于资讯、资料、文字、软件、音乐、照片、图形、等（下同）；<br />
　　2、散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的资料；<br />
　　3、冒充任何个人或机构，或以虚伪不实的方式误导他人以为其与任何人或任何机构有关；<br />
　　4、通过本网站干扰、破坏或限制他人计算机软件、硬件或通讯设备功能的行为；<br />
　　5、通过本网站跟踪或以其他方式骚扰他人；<br />
<strong>　　第四条 用户隐私</strong><br />
　　我们承诺，对您个人的信息和隐私的安全承担保密义务。未经您授权或同意，本网站不会将您的个人资料信息泄露给第三方，但以下情况除外，且本网站不承担任何责任：<br />
　　（一）政府单位按照中华人民共和国的法律、行政法规、部门规章、司法解释等规范性法律文件（统称&ldquo;法律法规&rdquo;），要求本网站提供的；<br />
　　（二）由于您将用户和密码告知或泄露给他人，由此导致的个人资料泄露；<br />
　　（三）包括但不仅限于黑客攻击、计算机病毒侵入或发作、政府管制等不可抗力而造成的用户个人资料泄露、丢失、被盗用或被篡改等；<br />
　　（四）您通过本网站链接其他网站造成的个人资料泄露以及由此导致的任何法律争议和后果；<br />
　　（五）为免除他人正在遭受威胁到其生命、身体或财产等方面的危险，所采取的措施；<br />
　　（六）本网站会与其他网站链接，但不对其他网站的隐私政策及内容负责；<br />
　　（七）此外，您若发现有任何非法使用您的用户账号或安全漏洞的情况，应立即通告本网站；<br />
　　（八）由于您自身的疏忽、大意等过错所导致的；<br />
　　（九）您在本网站的有关记录有可能成为您违反法律法规和本协议的证据；<br />
<strong>　　第五条 知识产权</strong><br />
　　本网站所有的域名、商号、商标、文字、视像及声音内容、图形及图像均受有关所有权和知识产权法律的保护，未经本网站事先以书面明确允许，任何个人或单位，均不得进行复制和使用。<br />
<strong>　　第六条 法律适用</strong><br />
　　（一）协议由本网站的所有人负责修订，并通过本网站公布，您的注册行为即被视为您自愿接受协议的全部条款，受其约束。<br />
　　（二）协议的生效、履行、解释及争议的解决均适用中华人民共和国法律。<br />
　　（三）您使用网站提供的服务如产生争议，原则上双方协商解决，协商不成可向本网站所有人所在的仲裁机构、人民法院进行调解或提起诉讼。<br />
　　（四）协议的条款如与法律相抵触，本网站可进行重新解析，而其他条款则保持对您产生法律效力和约束。<br />
&nbsp;</p>
',
    ],
    'intercept' => [
        'global_user' => false,
        'email_verify' => false,
        'mobile_verify' => false,
    ],
];
