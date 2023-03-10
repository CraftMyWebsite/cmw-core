/*! For license information please see editorjs-codeflask.bundle.js.LICENSE.txt */
!function (e, t) {
    "object" == typeof exports && "object" == typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define([], t) : "object" == typeof exports ? exports.editorjsCodeflask = t() : e.editorjsCodeflask = t()
}(self, (() => (() => {
    var e = {
        733: (e, t, n) => {
            "use strict";
            n.d(t, {Z: () => o});
            var a = n(81), i = n.n(a), s = n(645), r = n.n(s)()(i());
            r.push([e.id, '.nice-select{-webkit-tap-highlight-color:rgba(0,0,0,0);background-color:#fff;border-radius:5px;border:solid 1px #e8e8e8;box-sizing:border-box;clear:both;cursor:pointer;display:block;float:left;font-family:inherit;font-size:14px;font-weight:normal;height:38px;line-height:36px;outline:none;padding-left:18px;padding-right:30px;position:relative;text-align:left !important;transition:all .2s ease-in-out;user-select:none;white-space:nowrap;width:auto}.nice-select:hover{border-color:#dbdbdb}.nice-select:active,.nice-select.open,.nice-select:focus{border-color:#999}.nice-select:after{border-bottom:2px solid #999;border-right:2px solid #999;content:"";display:block;height:5px;margin-top:-4px;pointer-events:none;position:absolute;right:12px;top:50%;transform-origin:66% 66%;transform:rotate(45deg);transition:all .15s ease-in-out;width:5px}.nice-select.open:after{transform:rotate(-135deg)}.nice-select.open .nice-select-dropdown{opacity:1;pointer-events:auto;transform:scale(1) translateY(0)}.nice-select.disabled{border-color:#ededed;color:#999;pointer-events:none}.nice-select.disabled:after{border-color:#ccc}.nice-select.wide{width:100%}.nice-select.wide .nice-select-dropdown{left:0 !important;right:0 !important}.nice-select.right{float:right}.nice-select.right .nice-select-dropdown{left:auto;right:0}.nice-select.small{font-size:12px;height:36px;line-height:34px}.nice-select.small:after{height:4px;width:4px}.nice-select.small .option{line-height:34px;min-height:34px}.nice-select .nice-select-dropdown{margin-top:4px;background-color:#fff;border-radius:5px;box-shadow:0 0 0 1px rgba(68,68,68,.11);pointer-events:none;position:absolute;top:100%;left:0;transform-origin:50% 0;transform:scale(0.75) translateY(19px);transition:all .2s cubic-bezier(0.5, 0, 0, 1.25),opacity .15s ease-out;z-index:9;opacity:0}.nice-select .list{border-radius:5px;box-sizing:border-box;overflow:hidden;padding:0;max-height:210px;overflow-y:auto}.nice-select .list:hover .option:not(:hover){background-color:transparent !important}.nice-select .option{cursor:pointer;font-weight:400;line-height:40px;list-style:none;outline:none;padding-left:18px;padding-right:29px;text-align:left;transition:all .2s}.nice-select .option:hover,.nice-select .option.focus,.nice-select .option.selected.focus{background-color:#f6f6f6}.nice-select .option.selected{font-weight:bold}.nice-select .option.disabled{background-color:transparent;color:#999;cursor:default}.nice-select .optgroup{font-weight:bold}.no-csspointerevents .nice-select .nice-select-dropdown{display:none}.no-csspointerevents .nice-select.open .nice-select-dropdown{display:block}.nice-select .list::-webkit-scrollbar{width:0}.nice-select .has-multiple{white-space:inherit;height:auto;padding:7px 12px;min-height:36px;line-height:22px}.nice-select .has-multiple span.current{border:1px solid #ccc;background:#eee;padding:0 10px;border-radius:3px;display:inline-block;line-height:24px;font-size:14px;margin-bottom:3px;margin-right:3px}.nice-select .has-multiple .multiple-options{display:block;line-height:24px;padding:0}.nice-select .nice-select-search-box{box-sizing:border-box;width:100%;padding:5px;pointer-events:none;border-radius:5px 5px 0 0}.nice-select .nice-select-search{box-sizing:border-box;background-color:#fff;border:1px solid #e8e8e8;border-radius:3px;color:#444;display:inline-block;vertical-align:middle;padding:7px 12px;margin:0 10px 0 0;width:100%;min-height:36px;line-height:22px;height:auto;outline:0 !important;font-size:14px}\r\n', ""]);
            const o = r
        }, 738: (e, t, n) => {
            "use strict";
            n.d(t, {Z: () => o});
            var a = n(81), i = n.n(a), s = n(645), r = n.n(s)()(i());
            r.push([e.id, ".editorjs-codeFlask_Wrapper {\n    height: 200px;\n\tborder: 1px solid #dcdfe6;\n\tborder-radius: 5px;\n\tbackground-color: #f0f2f5;\n    margin-bottom: 10px;\n}\n\n.editorjs-codeFlask_Wrapper .codeflask {\n\tborder-radius: 5px;\n\tbackground: none;\n}\n\n\n.editorjs-codeFlask_Wrapper .editorjs-codeFlask_LangDisplay {\n\tposition: absolute;\n\theight: 20px;\n\tline-height: 20px;\n\tfont-size: 10px;\n\tcolor: #999;\n\tbackground-color: #dcdfe6;\n\tpadding: 5px;\n\tpadding-left: 10px;\n\tpadding-right: 10px;\n\tright: 0;\n\tbottom: 0;\n\tborder-bottom-right-radius: 5px;\n\tborder-top-left-radius: 5px;\n}\n\n.editorjs-codeFlask_Wrapper .codeflask.codeflask--has-line-numbers:before{\n    background-color: #dcdfe6;\n}", ""]);
            const o = r
        }, 645: e => {
            "use strict";
            e.exports = function (e) {
                var t = [];
                return t.toString = function () {
                    return this.map((function (t) {
                        var n = "", a = void 0 !== t[5];
                        return t[4] && (n += "@supports (".concat(t[4], ") {")), t[2] && (n += "@media ".concat(t[2], " {")), a && (n += "@layer".concat(t[5].length > 0 ? " ".concat(t[5]) : "", " {")), n += e(t), a && (n += "}"), t[2] && (n += "}"), t[4] && (n += "}"), n
                    })).join("")
                }, t.i = function (e, n, a, i, s) {
                    "string" == typeof e && (e = [[null, e, void 0]]);
                    var r = {};
                    if (a) for (var o = 0; o < this.length; o++) {
                        var l = this[o][0];
                        null != l && (r[l] = !0)
                    }
                    for (var d = 0; d < e.length; d++) {
                        var c = [].concat(e[d]);
                        a && r[c[0]] || (void 0 !== s && (void 0 === c[5] || (c[1] = "@layer".concat(c[5].length > 0 ? " ".concat(c[5]) : "", " {").concat(c[1], "}")), c[5] = s), n && (c[2] ? (c[1] = "@media ".concat(c[2], " {").concat(c[1], "}"), c[2] = n) : c[2] = n), i && (c[4] ? (c[1] = "@supports (".concat(c[4], ") {").concat(c[1], "}"), c[4] = i) : c[4] = "".concat(i)), t.push(c))
                    }
                }, t
            }
        }, 81: e => {
            "use strict";
            e.exports = function (e) {
                return e[1]
            }
        }, 668: e => {
            self, e.exports = (() => {
                "use strict";
                var e = {
                    d: (t, n) => {
                        for (var a in n) e.o(n, a) && !e.o(t, a) && Object.defineProperty(t, a, {
                            enumerable: !0,
                            get: n[a]
                        })
                    }, o: (e, t) => Object.prototype.hasOwnProperty.call(e, t), r: e => {
                        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
                    }
                }, t = {};

                function n(e) {
                    var t = document.createEvent("MouseEvents");
                    t.initEvent("click", !0, !1), e.dispatchEvent(t)
                }

                function a(e) {
                    var t = document.createEvent("HTMLEvents");
                    t.initEvent("change", !0, !1), e.dispatchEvent(t)
                }

                function i(e) {
                    var t = document.createEvent("FocusEvent");
                    t.initEvent("focusin", !0, !1), e.dispatchEvent(t)
                }

                function s(e) {
                    var t = document.createEvent("FocusEvent");
                    t.initEvent("focusout", !0, !1), e.dispatchEvent(t)
                }

                function r(e, t) {
                    return e.getAttribute(t)
                }

                function o(e, t) {
                    return !!e && e.classList.contains(t)
                }

                function l(e, t) {
                    if (e) return e.classList.add(t)
                }

                function d(e, t) {
                    if (e) return e.classList.remove(t)
                }

                e.r(t), e.d(t, {default: () => u, bind: () => p});
                var c = {data: null, searchable: !1};

                function u(e, t) {
                    this.el = e, this.config = Object.assign({}, c, t || {}), this.data = this.config.data, this.selectedOptions = [], this.placeholder = r(this.el, "placeholder") || this.config.placeholder || "Select an option", this.dropdown = null, this.multiple = r(this.el, "multiple"), this.disabled = r(this.el, "disabled"), this.create()
                }

                function p(e, t) {
                    return new u(e, t)
                }

                return u.prototype.create = function () {
                    this.el.style.display = "none", this.data ? this.processData(this.data) : this.extractData(), this.renderDropdown(), this.bindEvent()
                }, u.prototype.processData = function (e) {
                    var t = [];
                    e.forEach((e => {
                        t.push({data: e, attributes: {selected: !1, disabled: !1, optgroup: "optgroup" == e.value}})
                    })), this.options = t
                }, u.prototype.extractData = function () {
                    var e = this.el.querySelectorAll("option,optgroup"), t = [], n = [], a = [];
                    e.forEach((e => {
                        if ("OPTGROUP" == e.tagName) var a = {
                            text: e.label,
                            value: "optgroup"
                        }; else a = {text: e.innerText, value: e.value};
                        var i = {
                            selected: null != e.getAttribute("selected"),
                            disabled: null != e.getAttribute("disabled"),
                            optgroup: "OPTGROUP" == e.tagName
                        };
                        t.push(a), n.push({data: a, attributes: i})
                    })), this.data = t, this.options = n, this.options.forEach((function (e) {
                        e.attributes.selected && a.push(e)
                    })), this.selectedOptions = a
                }, u.prototype.renderDropdown = function () {
                    var e = `<div class="${["nice-select", r(this.el, "class") || "", this.disabled ? "disabled" : "", this.multiple ? "has-multiple" : ""].join(" ")}" tabindex="${this.disabled ? null : 0}">\n  <span class="${this.multiple ? "multiple-options" : "current"}"></span>\n  <div class="nice-select-dropdown">\n  ${this.config.searchable ? '<div class="nice-select-search-box">\n<input type="text" class="nice-select-search" placeholder="Search..."/>\n</div>' : ""}\n  <ul class="list"></ul>\n  </div></div>\n`;
                    this.el.insertAdjacentHTML("afterend", e), this.dropdown = this.el.nextElementSibling, this._renderSelectedItems(), this._renderItems()
                }, u.prototype._renderSelectedItems = function () {
                    if (this.multiple) {
                        var e = "";
                        "auto" == window.getComputedStyle(this.dropdown).width || this.selectedOptions.length < 2 ? (this.selectedOptions.forEach((function (t) {
                            e += `<span class="current">${t.data.text}</span>`
                        })), e = "" == e ? this.placeholder : e) : e = this.selectedOptions.length + " selected", this.dropdown.querySelector(".multiple-options").innerHTML = e
                    } else {
                        var t = this.selectedOptions.length > 0 ? this.selectedOptions[0].data.text : this.placeholder;
                        this.dropdown.querySelector(".current").innerHTML = t
                    }
                }, u.prototype._renderItems = function () {
                    var e = this.dropdown.querySelector("ul");
                    this.options.forEach((t => {
                        e.appendChild(this._renderItem(t))
                    }))
                }, u.prototype._renderItem = function (e) {
                    var t = document.createElement("li");
                    if (t.innerHTML = e.data.text, e.attributes.optgroup) t.classList.add("optgroup"); else {
                        t.setAttribute("data-value", e.data.value);
                        var n = ["option", e.attributes.selected ? "selected" : null, e.attributes.disabled ? "disabled" : null];
                        t.addEventListener("click", this._onItemClicked.bind(this, e)), t.classList.add(...n)
                    }
                    return e.element = t, t
                }, u.prototype.update = function () {
                    if (this.extractData(), this.dropdown) {
                        var e = o(this.dropdown, "open");
                        this.dropdown.parentNode.removeChild(this.dropdown), this.create(), e && n(this.dropdown)
                    }
                }, u.prototype.disable = function () {
                    this.disabled || (this.disabled = !0, l(this.dropdown, "disabled"))
                }, u.prototype.enable = function () {
                    this.disabled && (this.disabled = !1, d(this.dropdown, "disabled"))
                }, u.prototype.clear = function () {
                    this.selectedOptions = [], this._renderSelectedItems(), this.updateSelectValue(), a(this.el)
                }, u.prototype.destroy = function () {
                    this.dropdown && (this.dropdown.parentNode.removeChild(this.dropdown), this.el.style.display = "")
                }, u.prototype.bindEvent = function () {
                    this.dropdown.addEventListener("click", this._onClicked.bind(this)), this.dropdown.addEventListener("keydown", this._onKeyPressed.bind(this)), this.dropdown.addEventListener("focusin", i.bind(this, this.el)), this.dropdown.addEventListener("focusout", s.bind(this, this.el)), window.addEventListener("click", this._onClickedOutside.bind(this)), this.config.searchable && this._bindSearchEvent()
                }, u.prototype._bindSearchEvent = function () {
                    var e = this.dropdown.querySelector(".nice-select-search");
                    e && e.addEventListener("click", (function (e) {
                        return e.stopPropagation(), !1
                    })), e.addEventListener("input", this._onSearchChanged.bind(this))
                }, u.prototype._onClicked = function (e) {
                    if (this.multiple ? this.dropdown.classList.add("open") : this.dropdown.classList.toggle("open"), this.dropdown.classList.contains("open")) {
                        var t = this.dropdown.querySelector(".nice-select-search");
                        t && (t.value = "", t.focus());
                        var n = this.dropdown.querySelector(".focus");
                        d(n, "focus"), l(n = this.dropdown.querySelector(".selected"), "focus"), this.dropdown.querySelectorAll("ul li").forEach((function (e) {
                            e.style.display = ""
                        }))
                    } else this.dropdown.focus()
                }, u.prototype._onItemClicked = function (e, t) {
                    var n = t.target;
                    o(n, "disabled") || (this.multiple ? o(n, "selected") ? (d(n, "selected"), this.selectedOptions.splice(this.selectedOptions.indexOf(e), 1), this.el.querySelector('option[value="' + n.dataset.value + '"]').selected = !1) : (l(n, "selected"), this.selectedOptions.push(e)) : (this.selectedOptions.forEach((function (e) {
                        d(e.element, "selected")
                    })), l(n, "selected"), this.selectedOptions = [e]), this._renderSelectedItems(), this.updateSelectValue())
                }, u.prototype.updateSelectValue = function () {
                    if (this.multiple) {
                        var e = this.el;
                        this.selectedOptions.forEach((function (t) {
                            var n = e.querySelector('option[value="' + t.data.value + '"]');
                            n && n.setAttribute("selected", !0)
                        }))
                    } else this.selectedOptions.length > 0 && (this.el.value = this.selectedOptions[0].data.value);
                    a(this.el)
                }, u.prototype._onClickedOutside = function (e) {
                    this.dropdown.contains(e.target) || this.dropdown.classList.remove("open")
                }, u.prototype._onKeyPressed = function (e) {
                    var t = this.dropdown.querySelector(".focus"), a = this.dropdown.classList.contains("open");
                    if (32 == e.keyCode || 13 == e.keyCode) n(a ? t : this.dropdown); else if (40 == e.keyCode) {
                        if (a) {
                            var i = this._findNext(t);
                            i && (d(this.dropdown.querySelector(".focus"), "focus"), l(i, "focus"))
                        } else n(this.dropdown);
                        e.preventDefault()
                    } else if (38 == e.keyCode) {
                        if (a) {
                            var s = this._findPrev(t);
                            s && (d(this.dropdown.querySelector(".focus"), "focus"), l(s, "focus"))
                        } else n(this.dropdown);
                        e.preventDefault()
                    } else 27 == e.keyCode && a && n(this.dropdown);
                    return !1
                }, u.prototype._findNext = function (e) {
                    for (e = e ? e.nextElementSibling : this.dropdown.querySelector(".list .option"); e;) {
                        if (!o(e, "disabled") && "none" != e.style.display) return e;
                        e = e.nextElementSibling
                    }
                    return null
                }, u.prototype._findPrev = function (e) {
                    for (e = e ? e.previousElementSibling : this.dropdown.querySelector(".list .option:last-child"); e;) {
                        if (!o(e, "disabled") && "none" != e.style.display) return e;
                        e = e.previousElementSibling
                    }
                    return null
                }, u.prototype._onSearchChanged = function (e) {
                    var t = this.dropdown.classList.contains("open"), n = e.target.value;
                    if ("" == (n = n.toLowerCase())) this.options.forEach((function (e) {
                        e.element.style.display = ""
                    })); else if (t) {
                        var a = new RegExp(n);
                        this.options.forEach((function (e) {
                            var t = e.data.text.toLowerCase(), n = a.test(t);
                            e.element.style.display = n ? "" : "none"
                        }))
                    }
                    this.dropdown.querySelectorAll(".focus").forEach((function (e) {
                        d(e, "focus")
                    })), l(this._findNext(null), "focus")
                }, t
            })()
        }, 874: () => {
            !function (e) {
                var t = "\\b(?:BASH|BASHOPTS|BASH_ALIASES|BASH_ARGC|BASH_ARGV|BASH_CMDS|BASH_COMPLETION_COMPAT_DIR|BASH_LINENO|BASH_REMATCH|BASH_SOURCE|BASH_VERSINFO|BASH_VERSION|COLORTERM|COLUMNS|COMP_WORDBREAKS|DBUS_SESSION_BUS_ADDRESS|DEFAULTS_PATH|DESKTOP_SESSION|DIRSTACK|DISPLAY|EUID|GDMSESSION|GDM_LANG|GNOME_KEYRING_CONTROL|GNOME_KEYRING_PID|GPG_AGENT_INFO|GROUPS|HISTCONTROL|HISTFILE|HISTFILESIZE|HISTSIZE|HOME|HOSTNAME|HOSTTYPE|IFS|INSTANCE|JOB|LANG|LANGUAGE|LC_ADDRESS|LC_ALL|LC_IDENTIFICATION|LC_MEASUREMENT|LC_MONETARY|LC_NAME|LC_NUMERIC|LC_PAPER|LC_TELEPHONE|LC_TIME|LESSCLOSE|LESSOPEN|LINES|LOGNAME|LS_COLORS|MACHTYPE|MAILCHECK|MANDATORY_PATH|NO_AT_BRIDGE|OLDPWD|OPTERR|OPTIND|ORBIT_SOCKETDIR|OSTYPE|PAPERSIZE|PATH|PIPESTATUS|PPID|PS1|PS2|PS3|PS4|PWD|RANDOM|REPLY|SECONDS|SELINUX_INIT|SESSION|SESSIONTYPE|SESSION_MANAGER|SHELL|SHELLOPTS|SHLVL|SSH_AUTH_SOCK|TERM|UID|UPSTART_EVENTS|UPSTART_INSTANCE|UPSTART_JOB|UPSTART_SESSION|USER|WINDOWID|XAUTHORITY|XDG_CONFIG_DIRS|XDG_CURRENT_DESKTOP|XDG_DATA_DIRS|XDG_GREETER_DATA_DIR|XDG_MENU_PREFIX|XDG_RUNTIME_DIR|XDG_SEAT|XDG_SEAT_PATH|XDG_SESSION_DESKTOP|XDG_SESSION_ID|XDG_SESSION_PATH|XDG_SESSION_TYPE|XDG_VTNR|XMODIFIERS)\\b",
                    n = {pattern: /(^(["']?)\w+\2)[ \t]+\S.*/, lookbehind: !0, alias: "punctuation", inside: null},
                    a = {
                        bash: n,
                        environment: {pattern: RegExp("\\$" + t), alias: "constant"},
                        variable: [{
                            pattern: /\$?\(\([\s\S]+?\)\)/,
                            greedy: !0,
                            inside: {
                                variable: [{pattern: /(^\$\(\([\s\S]+)\)\)/, lookbehind: !0}, /^\$\(\(/],
                                number: /\b0x[\dA-Fa-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:[Ee]-?\d+)?/,
                                operator: /--|\+\+|\*\*=?|<<=?|>>=?|&&|\|\||[=!+\-*/%<>^&|]=?|[?~:]/,
                                punctuation: /\(\(?|\)\)?|,|;/
                            }
                        }, {
                            pattern: /\$\((?:\([^)]+\)|[^()])+\)|`[^`]+`/,
                            greedy: !0,
                            inside: {variable: /^\$\(|^`|\)$|`$/}
                        }, {
                            pattern: /\$\{[^}]+\}/,
                            greedy: !0,
                            inside: {
                                operator: /:[-=?+]?|[!\/]|##?|%%?|\^\^?|,,?/,
                                punctuation: /[\[\]]/,
                                environment: {pattern: RegExp("(\\{)" + t), lookbehind: !0, alias: "constant"}
                            }
                        }, /\$(?:\w+|[#?*!@$])/],
                        entity: /\\(?:[abceEfnrtv\\"]|O?[0-7]{1,3}|x[0-9a-fA-F]{1,2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8})/
                    };
                e.languages.bash = {
                    shebang: {pattern: /^#!\s*\/.*/, alias: "important"},
                    comment: {pattern: /(^|[^"{\\$])#.*/, lookbehind: !0},
                    "function-name": [{
                        pattern: /(\bfunction\s+)[\w-]+(?=(?:\s*\(?:\s*\))?\s*\{)/,
                        lookbehind: !0,
                        alias: "function"
                    }, {pattern: /\b[\w-]+(?=\s*\(\s*\)\s*\{)/, alias: "function"}],
                    "for-or-select": {
                        pattern: /(\b(?:for|select)\s+)\w+(?=\s+in\s)/,
                        alias: "variable",
                        lookbehind: !0
                    },
                    "assign-left": {
                        pattern: /(^|[\s;|&]|[<>]\()\w+(?=\+?=)/,
                        inside: {
                            environment: {
                                pattern: RegExp("(^|[\\s;|&]|[<>]\\()" + t),
                                lookbehind: !0,
                                alias: "constant"
                            }
                        },
                        alias: "variable",
                        lookbehind: !0
                    },
                    string: [{
                        pattern: /((?:^|[^<])<<-?\s*)(\w+)\s[\s\S]*?(?:\r?\n|\r)\2/,
                        lookbehind: !0,
                        greedy: !0,
                        inside: a
                    }, {
                        pattern: /((?:^|[^<])<<-?\s*)(["'])(\w+)\2\s[\s\S]*?(?:\r?\n|\r)\3/,
                        lookbehind: !0,
                        greedy: !0,
                        inside: {bash: n}
                    }, {
                        pattern: /(^|[^\\](?:\\\\)*)"(?:\\[\s\S]|\$\([^)]+\)|\$(?!\()|`[^`]+`|[^"\\`$])*"/,
                        lookbehind: !0,
                        greedy: !0,
                        inside: a
                    }, {pattern: /(^|[^$\\])'[^']*'/, lookbehind: !0, greedy: !0}, {
                        pattern: /\$'(?:[^'\\]|\\[\s\S])*'/,
                        greedy: !0,
                        inside: {entity: a.entity}
                    }],
                    environment: {pattern: RegExp("\\$?" + t), alias: "constant"},
                    variable: a.variable,
                    function: {
                        pattern: /(^|[\s;|&]|[<>]\()(?:add|apropos|apt|aptitude|apt-cache|apt-get|aspell|automysqlbackup|awk|basename|bash|bc|bconsole|bg|bzip2|cal|cat|cfdisk|chgrp|chkconfig|chmod|chown|chroot|cksum|clear|cmp|column|comm|composer|cp|cron|crontab|csplit|curl|cut|date|dc|dd|ddrescue|debootstrap|df|diff|diff3|dig|dir|dircolors|dirname|dirs|dmesg|du|egrep|eject|env|ethtool|expand|expect|expr|fdformat|fdisk|fg|fgrep|file|find|fmt|fold|format|free|fsck|ftp|fuser|gawk|git|gparted|grep|groupadd|groupdel|groupmod|groups|grub-mkconfig|gzip|halt|head|hg|history|host|hostname|htop|iconv|id|ifconfig|ifdown|ifup|import|install|ip|jobs|join|kill|killall|less|link|ln|locate|logname|logrotate|look|lpc|lpr|lprint|lprintd|lprintq|lprm|ls|lsof|lynx|make|man|mc|mdadm|mkconfig|mkdir|mke2fs|mkfifo|mkfs|mkisofs|mknod|mkswap|mmv|more|most|mount|mtools|mtr|mutt|mv|nano|nc|netstat|nice|nl|nohup|notify-send|npm|nslookup|op|open|parted|passwd|paste|pathchk|ping|pkill|pnpm|popd|pr|printcap|printenv|ps|pushd|pv|quota|quotacheck|quotactl|ram|rar|rcp|reboot|remsync|rename|renice|rev|rm|rmdir|rpm|rsync|scp|screen|sdiff|sed|sendmail|seq|service|sftp|sh|shellcheck|shuf|shutdown|sleep|slocate|sort|split|ssh|stat|strace|su|sudo|sum|suspend|swapon|sync|tac|tail|tar|tee|time|timeout|top|touch|tr|traceroute|tsort|tty|umount|uname|unexpand|uniq|units|unrar|unshar|unzip|update-grub|uptime|useradd|userdel|usermod|users|uudecode|uuencode|v|vdir|vi|vim|virsh|vmstat|wait|watch|wc|wget|whereis|which|who|whoami|write|xargs|xdg-open|yarn|yes|zenity|zip|zsh|zypper)(?=$|[)\s;|&])/,
                        lookbehind: !0
                    },
                    keyword: {
                        pattern: /(^|[\s;|&]|[<>]\()(?:if|then|else|elif|fi|for|while|in|case|esac|function|select|do|done|until)(?=$|[)\s;|&])/,
                        lookbehind: !0
                    },
                    builtin: {
                        pattern: /(^|[\s;|&]|[<>]\()(?:\.|:|break|cd|continue|eval|exec|exit|export|getopts|hash|pwd|readonly|return|shift|test|times|trap|umask|unset|alias|bind|builtin|caller|command|declare|echo|enable|help|let|local|logout|mapfile|printf|read|readarray|source|type|typeset|ulimit|unalias|set|shopt)(?=$|[)\s;|&])/,
                        lookbehind: !0,
                        alias: "class-name"
                    },
                    boolean: {pattern: /(^|[\s;|&]|[<>]\()(?:true|false)(?=$|[)\s;|&])/, lookbehind: !0},
                    "file-descriptor": {pattern: /\B&\d\b/, alias: "important"},
                    operator: {
                        pattern: /\d?<>|>\||\+=|=[=~]?|!=?|<<[<-]?|[&\d]?>>|\d[<>]&?|[<>][&=]?|&[>&]?|\|[&|]?/,
                        inside: {"file-descriptor": {pattern: /^\d/, alias: "important"}}
                    },
                    punctuation: /\$?\(\(?|\)\)?|\.\.|[{}[\];\\]/,
                    number: {pattern: /(^|\s)(?:[1-9]\d*|0)(?:[.,]\d+)?\b/, lookbehind: !0}
                }, n.inside = e.languages.bash;
                for (var i = ["comment", "function-name", "for-or-select", "assign-left", "string", "environment", "function", "keyword", "builtin", "boolean", "file-descriptor", "operator", "punctuation", "number"], s = a.variable[1].inside, r = 0; r < i.length; r++) s[i[r]] = e.languages.bash[i[r]];
                e.languages.shell = e.languages.bash
            }(Prism)
        }, 689: () => {
            Prism.languages.iecst = {
                comment: [{
                    pattern: /(^|[^\\])(?:\/\*[\s\S]*?(?:\*\/|$)|\(\*[\s\S]*?(?:\*\)|$)|\{[\s\S]*?(?:\}|$))/,
                    lookbehind: !0
                }, {pattern: /(^|[^\\:])\/\/.*/, lookbehind: !0, greedy: !0}],
                string: {pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/, greedy: !0},
                "class-name": /\b(?:END_)?(?:PROGRAM|CONFIGURATION|INTERFACE|FUNCTION_BLOCK|FUNCTION|ACTION|TRANSITION|TYPE|STRUCT|(?:INITIAL_)?STEP|NAMESPACE|LIBRARY|CHANNEL|FOLDER|RESOURCE|VAR_(?:GLOBAL|INPUT|PUTPUT|IN_OUT|ACCESS|TEMP|EXTERNAL|CONFIG)|VAR|METHOD|PROPERTY)\b/i,
                keyword: /\b(?:(?:END_)?(?:IF|WHILE|REPEAT|CASE|FOR)|ELSE|FROM|THEN|ELSIF|DO|TO|BY|PRIVATE|PUBLIC|PROTECTED|CONSTANT|RETURN|EXIT|CONTINUE|GOTO|JMP|AT|RETAIN|NON_RETAIN|TASK|WITH|UNTIL|USING|EXTENDS|IMPLEMENTS|GET|SET|__TRY|__CATCH|__FINALLY|__ENDTRY)\b/,
                variable: /\b(?:AT|BOOL|BYTE|(?:D|L)?WORD|U?(?:S|D|L)?INT|L?REAL|TIME(?:_OF_DAY)?|TOD|DT|DATE(?:_AND_TIME)?|STRING|ARRAY|ANY|POINTER)\b/,
                symbol: /%[IQM][XBWDL][\d.]*|%[IQ][\d.]*/,
                number: /\b(?:16#[\da-f]+|2#[01_]+|0x[\da-f]+)\b|\b(?:T|D|DT|TOD)#[\d_shmd:]*|\b[A-Z]*#[\d.,_]*|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,
                boolean: /\b(?:TRUE|FALSE|NULL)\b/,
                function: /\w+(?=\()/,
                operator: /(?:S?R?:?=>?|&&?|\*\*?|<=?|>=?|[-:^/+])|\b(?:OR|AND|MOD|NOT|XOR|LE|GE|EQ|NE|GT|LT)\b/,
                punctuation: /[();]/,
                type: {pattern: /#/, alias: "selector"}
            }
        }, 277: () => {
            Prism.languages.json = {
                property: {
                    pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?=\s*:)/,
                    lookbehind: !0,
                    greedy: !0
                },
                string: {pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?!\s*:)/, lookbehind: !0, greedy: !0},
                comment: {pattern: /\/\/.*|\/\*[\s\S]*?(?:\*\/|$)/, greedy: !0},
                number: /-?\b\d+(?:\.\d+)?(?:e[+-]?\d+)?\b/i,
                punctuation: /[{}[\],]/,
                operator: /:/,
                boolean: /\b(?:true|false)\b/,
                null: {pattern: /\bnull\b/, alias: "keyword"}
            }, Prism.languages.webmanifest = Prism.languages.json
        }, 64: () => {
            !function (e) {
                var t = /(?:\\.|[^\\\n\r]|(?:\n|\r\n?)(?![\r\n]))/.source;

                function n(e) {
                    return e = e.replace(/<inner>/g, (function () {
                        return t
                    })), RegExp(/((?:^|[^\\])(?:\\{2})*)/.source + "(?:" + e + ")")
                }

                var a = /(?:\\.|``(?:[^`\r\n]|`(?!`))+``|`[^`\r\n]+`|[^\\|\r\n`])+/.source,
                    i = /\|?__(?:\|__)+\|?(?:(?:\n|\r\n?)|(?![\s\S]))/.source.replace(/__/g, (function () {
                        return a
                    })), s = /\|?[ \t]*:?-{3,}:?[ \t]*(?:\|[ \t]*:?-{3,}:?[ \t]*)+\|?(?:\n|\r\n?)/.source;
                e.languages.markdown = e.languages.extend("markup", {}), e.languages.insertBefore("markdown", "prolog", {
                    "front-matter-block": {
                        pattern: /(^(?:\s*[\r\n])?)---(?!.)[\s\S]*?[\r\n]---(?!.)/,
                        lookbehind: !0,
                        greedy: !0,
                        inside: {
                            punctuation: /^---|---$/,
                            "font-matter": {
                                pattern: /\S+(?:\s+\S+)*/,
                                alias: ["yaml", "language-yaml"],
                                inside: e.languages.yaml
                            }
                        }
                    },
                    blockquote: {pattern: /^>(?:[\t ]*>)*/m, alias: "punctuation"},
                    table: {
                        pattern: RegExp("^" + i + s + "(?:" + i + ")*", "m"),
                        inside: {
                            "table-data-rows": {
                                pattern: RegExp("^(" + i + s + ")(?:" + i + ")*$"),
                                lookbehind: !0,
                                inside: {
                                    "table-data": {pattern: RegExp(a), inside: e.languages.markdown},
                                    punctuation: /\|/
                                }
                            },
                            "table-line": {
                                pattern: RegExp("^(" + i + ")" + s + "$"),
                                lookbehind: !0,
                                inside: {punctuation: /\||:?-{3,}:?/}
                            },
                            "table-header-row": {
                                pattern: RegExp("^" + i + "$"),
                                inside: {
                                    "table-header": {
                                        pattern: RegExp(a),
                                        alias: "important",
                                        inside: e.languages.markdown
                                    }, punctuation: /\|/
                                }
                            }
                        }
                    },
                    code: [{
                        pattern: /((?:^|\n)[ \t]*\n|(?:^|\r\n?)[ \t]*\r\n?)(?: {4}|\t).+(?:(?:\n|\r\n?)(?: {4}|\t).+)*/,
                        lookbehind: !0,
                        alias: "keyword"
                    }, {
                        pattern: /^```[\s\S]*?^```$/m,
                        greedy: !0,
                        inside: {
                            "code-block": {
                                pattern: /^(```.*(?:\n|\r\n?))[\s\S]+?(?=(?:\n|\r\n?)^```$)/m,
                                lookbehind: !0
                            }, "code-language": {pattern: /^(```).+/, lookbehind: !0}, punctuation: /```/
                        }
                    }],
                    title: [{
                        pattern: /\S.*(?:\n|\r\n?)(?:==+|--+)(?=[ \t]*$)/m,
                        alias: "important",
                        inside: {punctuation: /==+$|--+$/}
                    }, {pattern: /(^\s*)#.+/m, lookbehind: !0, alias: "important", inside: {punctuation: /^#+|#+$/}}],
                    hr: {pattern: /(^\s*)([*-])(?:[\t ]*\2){2,}(?=\s*$)/m, lookbehind: !0, alias: "punctuation"},
                    list: {pattern: /(^\s*)(?:[*+-]|\d+\.)(?=[\t ].)/m, lookbehind: !0, alias: "punctuation"},
                    "url-reference": {
                        pattern: /!?\[[^\]]+\]:[\t ]+(?:\S+|<(?:\\.|[^>\\])+>)(?:[\t ]+(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\)))?/,
                        inside: {
                            variable: {pattern: /^(!?\[)[^\]]+/, lookbehind: !0},
                            string: /(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\))$/,
                            punctuation: /^[\[\]!:]|[<>]/
                        },
                        alias: "url"
                    },
                    bold: {
                        pattern: n(/\b__(?:(?!_)<inner>|_(?:(?!_)<inner>)+_)+__\b|\*\*(?:(?!\*)<inner>|\*(?:(?!\*)<inner>)+\*)+\*\*/.source),
                        lookbehind: !0,
                        greedy: !0,
                        inside: {
                            content: {pattern: /(^..)[\s\S]+(?=..$)/, lookbehind: !0, inside: {}},
                            punctuation: /\*\*|__/
                        }
                    },
                    italic: {
                        pattern: n(/\b_(?:(?!_)<inner>|__(?:(?!_)<inner>)+__)+_\b|\*(?:(?!\*)<inner>|\*\*(?:(?!\*)<inner>)+\*\*)+\*/.source),
                        lookbehind: !0,
                        greedy: !0,
                        inside: {
                            content: {pattern: /(^.)[\s\S]+(?=.$)/, lookbehind: !0, inside: {}},
                            punctuation: /[*_]/
                        }
                    },
                    strike: {
                        pattern: n(/(~~?)(?:(?!~)<inner>)+\2/.source),
                        lookbehind: !0,
                        greedy: !0,
                        inside: {
                            content: {pattern: /(^~~?)[\s\S]+(?=\1$)/, lookbehind: !0, inside: {}},
                            punctuation: /~~?/
                        }
                    },
                    "code-snippet": {
                        pattern: /(^|[^\\`])(?:``[^`\r\n]+(?:`[^`\r\n]+)*``(?!`)|`[^`\r\n]+`(?!`))/,
                        lookbehind: !0,
                        greedy: !0,
                        alias: ["code", "keyword"]
                    },
                    url: {
                        pattern: n(/!?\[(?:(?!\])<inner>)+\](?:\([^\s)]+(?:[\t ]+"(?:\\.|[^"\\])*")?\)|[ \t]?\[(?:(?!\])<inner>)+\])/.source),
                        lookbehind: !0,
                        greedy: !0,
                        inside: {
                            operator: /^!/,
                            content: {pattern: /(^\[)[^\]]+(?=\])/, lookbehind: !0, inside: {}},
                            variable: {pattern: /(^\][ \t]?\[)[^\]]+(?=\]$)/, lookbehind: !0},
                            url: {pattern: /(^\]\()[^\s)]+/, lookbehind: !0},
                            string: {pattern: /(^[ \t]+)"(?:\\.|[^"\\])*"(?=\)$)/, lookbehind: !0}
                        }
                    }
                }), ["url", "bold", "italic", "strike"].forEach((function (t) {
                    ["url", "bold", "italic", "strike", "code-snippet"].forEach((function (n) {
                        t !== n && (e.languages.markdown[t].inside.content.inside[n] = e.languages.markdown[n])
                    }))
                })), e.hooks.add("after-tokenize", (function (e) {
                    "markdown" !== e.language && "md" !== e.language || function e(t) {
                        if (t && "string" != typeof t) for (var n = 0, a = t.length; n < a; n++) {
                            var i = t[n];
                            if ("code" === i.type) {
                                var s = i.content[1], r = i.content[3];
                                if (s && r && "code-language" === s.type && "code-block" === r.type && "string" == typeof s.content) {
                                    var o = s.content.replace(/\b#/g, "sharp").replace(/\b\+\+/g, "pp"),
                                        l = "language-" + (o = (/[a-z][\w-]*/i.exec(o) || [""])[0].toLowerCase());
                                    r.alias ? "string" == typeof r.alias ? r.alias = [r.alias, l] : r.alias.push(l) : r.alias = [l]
                                }
                            } else e(i.content)
                        }
                    }(e.tokens)
                })), e.hooks.add("wrap", (function (t) {
                    if ("code-block" === t.type) {
                        for (var n = "", a = 0, i = t.classes.length; a < i; a++) {
                            var s = t.classes[a], d = /language-(.+)/.exec(s);
                            if (d) {
                                n = d[1];
                                break
                            }
                        }
                        var c = e.languages[n];
                        if (c) t.content = e.highlight(t.content.replace(r, "").replace(/&(\w{1,8}|#x?[\da-f]{1,8});/gi, (function (e, t) {
                            var n;
                            return "#" === (t = t.toLowerCase())[0] ? (n = "x" === t[1] ? parseInt(t.slice(2), 16) : Number(t.slice(1)), l(n)) : o[t] || e
                        })), c, n); else if (n && "none" !== n && e.plugins.autoloader) {
                            var u = "md-" + (new Date).valueOf() + "-" + Math.floor(1e16 * Math.random());
                            t.attributes.id = u, e.plugins.autoloader.loadLanguages(n, (function () {
                                var t = document.getElementById(u);
                                t && (t.innerHTML = e.highlight(t.textContent, e.languages[n], n))
                            }))
                        }
                    }
                }));
                var r = RegExp(e.languages.markup.tag.pattern.source, "gi"),
                    o = {amp: "&", lt: "<", gt: ">", quot: '"'}, l = String.fromCodePoint || String.fromCharCode;
                e.languages.md = e.languages.markdown
            }(Prism)
        }, 335: () => {
            Prism.languages.markup = {
                comment: /<!--[\s\S]*?-->/,
                prolog: /<\?[\s\S]+?\?>/,
                doctype: {
                    pattern: /<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,
                    greedy: !0,
                    inside: {
                        "internal-subset": {
                            pattern: /(^[^\[]*\[)[\s\S]+(?=\]>$)/,
                            lookbehind: !0,
                            greedy: !0,
                            inside: null
                        },
                        string: {pattern: /"[^"]*"|'[^']*'/, greedy: !0},
                        punctuation: /^<!|>$|[[\]]/,
                        "doctype-tag": /^DOCTYPE/,
                        name: /[^\s<>'"]+/
                    }
                },
                cdata: /<!\[CDATA\[[\s\S]*?\]\]>/i,
                tag: {
                    pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
                    greedy: !0,
                    inside: {
                        tag: {
                            pattern: /^<\/?[^\s>\/]+/,
                            inside: {punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/}
                        },
                        "special-attr": [],
                        "attr-value": {
                            pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,
                            inside: {punctuation: [{pattern: /^=/, alias: "attr-equals"}, /"|'/]}
                        },
                        punctuation: /\/?>/,
                        "attr-name": {pattern: /[^\s>\/]+/, inside: {namespace: /^[^\s>\/:]+:/}}
                    }
                },
                entity: [{pattern: /&[\da-z]{1,8};/i, alias: "named-entity"}, /&#x?[\da-f]{1,8};/i]
            }, Prism.languages.markup.tag.inside["attr-value"].inside.entity = Prism.languages.markup.entity, Prism.languages.markup.doctype.inside["internal-subset"].inside = Prism.languages.markup, Prism.hooks.add("wrap", (function (e) {
                "entity" === e.type && (e.attributes.title = e.content.replace(/&amp;/, "&"))
            })), Object.defineProperty(Prism.languages.markup.tag, "addInlined", {
                value: function (e, t) {
                    var n = {};
                    n["language-" + t] = {
                        pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,
                        lookbehind: !0,
                        inside: Prism.languages[t]
                    }, n.cdata = /^<!\[CDATA\[|\]\]>$/i;
                    var a = {"included-cdata": {pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, inside: n}};
                    a["language-" + t] = {pattern: /[\s\S]+/, inside: Prism.languages[t]};
                    var i = {};
                    i[e] = {
                        pattern: RegExp(/(<__[^>]*>)(?:<!\[CDATA\[(?:[^\]]|\](?!\]>))*\]\]>|(?!<!\[CDATA\[)[\s\S])*?(?=<\/__>)/.source.replace(/__/g, (function () {
                            return e
                        })), "i"), lookbehind: !0, greedy: !0, inside: a
                    }, Prism.languages.insertBefore("markup", "cdata", i)
                }
            }), Object.defineProperty(Prism.languages.markup.tag, "addAttribute", {
                value: function (e, t) {
                    Prism.languages.markup.tag.inside["special-attr"].push({
                        pattern: RegExp(/(^|["'\s])/.source + "(?:" + e + ")" + /\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))/.source, "i"),
                        lookbehind: !0,
                        inside: {
                            "attr-name": /^[^\s=]+/,
                            "attr-value": {
                                pattern: /=[\s\S]+/,
                                inside: {
                                    value: {
                                        pattern: /(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,
                                        lookbehind: !0,
                                        alias: [t, "language-" + t],
                                        inside: Prism.languages[t]
                                    }, punctuation: [{pattern: /^=/, alias: "attr-equals"}, /"|'/]
                                }
                            }
                        }
                    })
                }
            }), Prism.languages.html = Prism.languages.markup, Prism.languages.mathml = Prism.languages.markup, Prism.languages.svg = Prism.languages.markup, Prism.languages.xml = Prism.languages.extend("markup", {}), Prism.languages.ssml = Prism.languages.xml, Prism.languages.atom = Prism.languages.xml, Prism.languages.rss = Prism.languages.xml
        }, 366: () => {
            Prism.languages.python = {
                comment: {pattern: /(^|[^\\])#.*/, lookbehind: !0},
                "string-interpolation": {
                    pattern: /(?:f|rf|fr)(?:("""|''')[\s\S]*?\1|("|')(?:\\.|(?!\2)[^\\\r\n])*\2)/i,
                    greedy: !0,
                    inside: {
                        interpolation: {
                            pattern: /((?:^|[^{])(?:\{\{)*)\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}])+\})+\})+\}/,
                            lookbehind: !0,
                            inside: {
                                "format-spec": {pattern: /(:)[^:(){}]+(?=\}$)/, lookbehind: !0},
                                "conversion-option": {pattern: /![sra](?=[:}]$)/, alias: "punctuation"},
                                rest: null
                            }
                        }, string: /[\s\S]+/
                    }
                },
                "triple-quoted-string": {pattern: /(?:[rub]|rb|br)?("""|''')[\s\S]*?\1/i, greedy: !0, alias: "string"},
                string: {pattern: /(?:[rub]|rb|br)?("|')(?:\\.|(?!\1)[^\\\r\n])*\1/i, greedy: !0},
                function: {pattern: /((?:^|\s)def[ \t]+)[a-zA-Z_]\w*(?=\s*\()/g, lookbehind: !0},
                "class-name": {pattern: /(\bclass\s+)\w+/i, lookbehind: !0},
                decorator: {
                    pattern: /(^[\t ]*)@\w+(?:\.\w+)*/im,
                    lookbehind: !0,
                    alias: ["annotation", "punctuation"],
                    inside: {punctuation: /\./}
                },
                keyword: /\b(?:and|as|assert|async|await|break|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|nonlocal|not|or|pass|print|raise|return|try|while|with|yield)\b/,
                builtin: /\b(?:__import__|abs|all|any|apply|ascii|basestring|bin|bool|buffer|bytearray|bytes|callable|chr|classmethod|cmp|coerce|compile|complex|delattr|dict|dir|divmod|enumerate|eval|execfile|file|filter|float|format|frozenset|getattr|globals|hasattr|hash|help|hex|id|input|int|intern|isinstance|issubclass|iter|len|list|locals|long|map|max|memoryview|min|next|object|oct|open|ord|pow|property|range|raw_input|reduce|reload|repr|reversed|round|set|setattr|slice|sorted|staticmethod|str|sum|super|tuple|type|unichr|unicode|vars|xrange|zip)\b/,
                boolean: /\b(?:True|False|None)\b/,
                number: /(?:\b(?=\d)|\B(?=\.))(?:0[bo])?(?:(?:\d|0x[\da-f])[\da-f]*(?:\.\d*)?|\.\d+)(?:e[+-]?\d+)?j?\b/i,
                operator: /[-+%=]=?|!=|\*\*?=?|\/\/?=?|<[<=>]?|>[=>]?|[&|^~]/,
                punctuation: /[{}[\];(),.:]/
            }, Prism.languages.python["string-interpolation"].inside.interpolation.inside.rest = Prism.languages.python, Prism.languages.py = Prism.languages.python
        }, 660: (e, t, n) => {
            var a = function (e) {
                var t = /\blang(?:uage)?-([\w-]+)\b/i, n = 0, a = {}, i = {
                    manual: e.Prism && e.Prism.manual,
                    disableWorkerMessageHandler: e.Prism && e.Prism.disableWorkerMessageHandler,
                    util: {
                        encode: function e(t) {
                            return t instanceof s ? new s(t.type, e(t.content), t.alias) : Array.isArray(t) ? t.map(e) : t.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/\u00a0/g, " ")
                        }, type: function (e) {
                            return Object.prototype.toString.call(e).slice(8, -1)
                        }, objId: function (e) {
                            return e.__id || Object.defineProperty(e, "__id", {value: ++n}), e.__id
                        }, clone: function e(t, n) {
                            var a, s;
                            switch (n = n || {}, i.util.type(t)) {
                                case"Object":
                                    if (s = i.util.objId(t), n[s]) return n[s];
                                    for (var r in a = {}, n[s] = a, t) t.hasOwnProperty(r) && (a[r] = e(t[r], n));
                                    return a;
                                case"Array":
                                    return s = i.util.objId(t), n[s] ? n[s] : (a = [], n[s] = a, t.forEach((function (t, i) {
                                        a[i] = e(t, n)
                                    })), a);
                                default:
                                    return t
                            }
                        }, getLanguage: function (e) {
                            for (; e && !t.test(e.className);) e = e.parentElement;
                            return e ? (e.className.match(t) || [, "none"])[1].toLowerCase() : "none"
                        }, currentScript: function () {
                            if ("undefined" == typeof document) return null;
                            if ("currentScript" in document) return document.currentScript;
                            try {
                                throw new Error
                            } catch (a) {
                                var e = (/at [^(\r\n]*\((.*):.+:.+\)$/i.exec(a.stack) || [])[1];
                                if (e) {
                                    var t = document.getElementsByTagName("script");
                                    for (var n in t) if (t[n].src == e) return t[n]
                                }
                                return null
                            }
                        }, isActive: function (e, t, n) {
                            for (var a = "no-" + t; e;) {
                                var i = e.classList;
                                if (i.contains(t)) return !0;
                                if (i.contains(a)) return !1;
                                e = e.parentElement
                            }
                            return !!n
                        }
                    },
                    languages: {
                        plain: a, plaintext: a, text: a, txt: a, extend: function (e, t) {
                            var n = i.util.clone(i.languages[e]);
                            for (var a in t) n[a] = t[a];
                            return n
                        }, insertBefore: function (e, t, n, a) {
                            var s = (a = a || i.languages)[e], r = {};
                            for (var o in s) if (s.hasOwnProperty(o)) {
                                if (o == t) for (var l in n) n.hasOwnProperty(l) && (r[l] = n[l]);
                                n.hasOwnProperty(o) || (r[o] = s[o])
                            }
                            var d = a[e];
                            return a[e] = r, i.languages.DFS(i.languages, (function (t, n) {
                                n === d && t != e && (this[t] = r)
                            })), r
                        }, DFS: function e(t, n, a, s) {
                            s = s || {};
                            var r = i.util.objId;
                            for (var o in t) if (t.hasOwnProperty(o)) {
                                n.call(t, o, t[o], a || o);
                                var l = t[o], d = i.util.type(l);
                                "Object" !== d || s[r(l)] ? "Array" !== d || s[r(l)] || (s[r(l)] = !0, e(l, n, o, s)) : (s[r(l)] = !0, e(l, n, null, s))
                            }
                        }
                    },
                    plugins: {},
                    highlightAll: function (e, t) {
                        i.highlightAllUnder(document, e, t)
                    },
                    highlightAllUnder: function (e, t, n) {
                        var a = {
                            callback: n,
                            container: e,
                            selector: 'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'
                        };
                        i.hooks.run("before-highlightall", a), a.elements = Array.prototype.slice.apply(a.container.querySelectorAll(a.selector)), i.hooks.run("before-all-elements-highlight", a);
                        for (var s, r = 0; s = a.elements[r++];) i.highlightElement(s, !0 === t, a.callback)
                    },
                    highlightElement: function (n, a, s) {
                        var r = i.util.getLanguage(n), o = i.languages[r];
                        n.className = n.className.replace(t, "").replace(/\s+/g, " ") + " language-" + r;
                        var l = n.parentElement;
                        l && "pre" === l.nodeName.toLowerCase() && (l.className = l.className.replace(t, "").replace(/\s+/g, " ") + " language-" + r);
                        var d = {element: n, language: r, grammar: o, code: n.textContent};

                        function c(e) {
                            d.highlightedCode = e, i.hooks.run("before-insert", d), d.element.innerHTML = d.highlightedCode, i.hooks.run("after-highlight", d), i.hooks.run("complete", d), s && s.call(d.element)
                        }

                        if (i.hooks.run("before-sanity-check", d), (l = d.element.parentElement) && "pre" === l.nodeName.toLowerCase() && !l.hasAttribute("tabindex") && l.setAttribute("tabindex", "0"), !d.code) return i.hooks.run("complete", d), void (s && s.call(d.element));
                        if (i.hooks.run("before-highlight", d), d.grammar) if (a && e.Worker) {
                            var u = new Worker(i.filename);
                            u.onmessage = function (e) {
                                c(e.data)
                            }, u.postMessage(JSON.stringify({language: d.language, code: d.code, immediateClose: !0}))
                        } else c(i.highlight(d.code, d.grammar, d.language)); else c(i.util.encode(d.code))
                    },
                    highlight: function (e, t, n) {
                        var a = {code: e, grammar: t, language: n};
                        return i.hooks.run("before-tokenize", a), a.tokens = i.tokenize(a.code, a.grammar), i.hooks.run("after-tokenize", a), s.stringify(i.util.encode(a.tokens), a.language)
                    },
                    tokenize: function (e, t) {
                        var n = t.rest;
                        if (n) {
                            for (var a in n) t[a] = n[a];
                            delete t.rest
                        }
                        var i = new l;
                        return d(i, i.head, e), o(e, i, t, i.head, 0), function (e) {
                            for (var t = [], n = e.head.next; n !== e.tail;) t.push(n.value), n = n.next;
                            return t
                        }(i)
                    },
                    hooks: {
                        all: {}, add: function (e, t) {
                            var n = i.hooks.all;
                            n[e] = n[e] || [], n[e].push(t)
                        }, run: function (e, t) {
                            var n = i.hooks.all[e];
                            if (n && n.length) for (var a, s = 0; a = n[s++];) a(t)
                        }
                    },
                    Token: s
                };

                function s(e, t, n, a) {
                    this.type = e, this.content = t, this.alias = n, this.length = 0 | (a || "").length
                }

                function r(e, t, n, a) {
                    e.lastIndex = t;
                    var i = e.exec(n);
                    if (i && a && i[1]) {
                        var s = i[1].length;
                        i.index += s, i[0] = i[0].slice(s)
                    }
                    return i
                }

                function o(e, t, n, a, l, u) {
                    for (var p in n) if (n.hasOwnProperty(p) && n[p]) {
                        var g = n[p];
                        g = Array.isArray(g) ? g : [g];
                        for (var h = 0; h < g.length; ++h) {
                            if (u && u.cause == p + "," + h) return;
                            var f = g[h], m = f.inside, b = !!f.lookbehind, y = !!f.greedy, v = f.alias;
                            if (y && !f.pattern.global) {
                                var k = f.pattern.toString().match(/[imsuy]*$/)[0];
                                f.pattern = RegExp(f.pattern.source, k + "g")
                            }
                            for (var x = f.pattern || f, w = a.next, S = l; w !== t.tail && !(u && S >= u.reach); S += w.value.length, w = w.next) {
                                var E = w.value;
                                if (t.length > e.length) return;
                                if (!(E instanceof s)) {
                                    var A, _ = 1;
                                    if (y) {
                                        if (!(A = r(x, S, e, b))) break;
                                        var T = A.index, F = A.index + A[0].length, C = S;
                                        for (C += w.value.length; T >= C;) C += (w = w.next).value.length;
                                        if (S = C -= w.value.length, w.value instanceof s) continue;
                                        for (var L = w; L !== t.tail && (C < F || "string" == typeof L.value); L = L.next) _++, C += L.value.length;
                                        _--, E = e.slice(S, C), A.index -= S
                                    } else if (!(A = r(x, 0, E, b))) continue;
                                    T = A.index;
                                    var O = A[0], N = E.slice(0, T), P = E.slice(T + O.length), I = S + E.length;
                                    u && I > u.reach && (u.reach = I);
                                    var $ = w.prev;
                                    if (N && ($ = d(t, $, N), S += N.length), c(t, $, _), w = d(t, $, new s(p, m ? i.tokenize(O, m) : O, v, O)), P && d(t, w, P), _ > 1) {
                                        var R = {cause: p + "," + h, reach: I};
                                        o(e, t, n, w.prev, S, R), u && R.reach > u.reach && (u.reach = R.reach)
                                    }
                                }
                            }
                        }
                    }
                }

                function l() {
                    var e = {value: null, prev: null, next: null}, t = {value: null, prev: e, next: null};
                    e.next = t, this.head = e, this.tail = t, this.length = 0
                }

                function d(e, t, n) {
                    var a = t.next, i = {value: n, prev: t, next: a};
                    return t.next = i, a.prev = i, e.length++, i
                }

                function c(e, t, n) {
                    for (var a = t.next, i = 0; i < n && a !== e.tail; i++) a = a.next;
                    t.next = a, a.prev = t, e.length -= i
                }

                if (e.Prism = i, s.stringify = function e(t, n) {
                    if ("string" == typeof t) return t;
                    if (Array.isArray(t)) {
                        var a = "";
                        return t.forEach((function (t) {
                            a += e(t, n)
                        })), a
                    }
                    var s = {
                        type: t.type,
                        content: e(t.content, n),
                        tag: "span",
                        classes: ["token", t.type],
                        attributes: {},
                        language: n
                    }, r = t.alias;
                    r && (Array.isArray(r) ? Array.prototype.push.apply(s.classes, r) : s.classes.push(r)), i.hooks.run("wrap", s);
                    var o = "";
                    for (var l in s.attributes) o += " " + l + '="' + (s.attributes[l] || "").replace(/"/g, "&quot;") + '"';
                    return "<" + s.tag + ' class="' + s.classes.join(" ") + '"' + o + ">" + s.content + "</" + s.tag + ">"
                }, !e.document) return e.addEventListener ? (i.disableWorkerMessageHandler || e.addEventListener("message", (function (t) {
                    var n = JSON.parse(t.data), a = n.language, s = n.code, r = n.immediateClose;
                    e.postMessage(i.highlight(s, i.languages[a], a)), r && e.close()
                }), !1), i) : i;
                var u = i.util.currentScript();

                function p() {
                    i.manual || i.highlightAll()
                }

                if (u && (i.filename = u.src, u.hasAttribute("data-manual") && (i.manual = !0)), !i.manual) {
                    var g = document.readyState;
                    "loading" === g || "interactive" === g && u && u.defer ? document.addEventListener("DOMContentLoaded", p) : window.requestAnimationFrame ? window.requestAnimationFrame(p) : window.setTimeout(p, 16)
                }
                return i
            }("undefined" != typeof window ? window : "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope ? self : {});
            e.exports && (e.exports = a), void 0 !== n.g && (n.g.Prism = a), a.languages.markup = {
                comment: /<!--[\s\S]*?-->/,
                prolog: /<\?[\s\S]+?\?>/,
                doctype: {
                    pattern: /<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,
                    greedy: !0,
                    inside: {
                        "internal-subset": {
                            pattern: /(^[^\[]*\[)[\s\S]+(?=\]>$)/,
                            lookbehind: !0,
                            greedy: !0,
                            inside: null
                        },
                        string: {pattern: /"[^"]*"|'[^']*'/, greedy: !0},
                        punctuation: /^<!|>$|[[\]]/,
                        "doctype-tag": /^DOCTYPE/,
                        name: /[^\s<>'"]+/
                    }
                },
                cdata: /<!\[CDATA\[[\s\S]*?\]\]>/i,
                tag: {
                    pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
                    greedy: !0,
                    inside: {
                        tag: {
                            pattern: /^<\/?[^\s>\/]+/,
                            inside: {punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/}
                        },
                        "special-attr": [],
                        "attr-value": {
                            pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,
                            inside: {punctuation: [{pattern: /^=/, alias: "attr-equals"}, /"|'/]}
                        },
                        punctuation: /\/?>/,
                        "attr-name": {pattern: /[^\s>\/]+/, inside: {namespace: /^[^\s>\/:]+:/}}
                    }
                },
                entity: [{pattern: /&[\da-z]{1,8};/i, alias: "named-entity"}, /&#x?[\da-f]{1,8};/i]
            }, a.languages.markup.tag.inside["attr-value"].inside.entity = a.languages.markup.entity, a.languages.markup.doctype.inside["internal-subset"].inside = a.languages.markup, a.hooks.add("wrap", (function (e) {
                "entity" === e.type && (e.attributes.title = e.content.replace(/&amp;/, "&"))
            })), Object.defineProperty(a.languages.markup.tag, "addInlined", {
                value: function (e, t) {
                    var n = {};
                    n["language-" + t] = {
                        pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,
                        lookbehind: !0,
                        inside: a.languages[t]
                    }, n.cdata = /^<!\[CDATA\[|\]\]>$/i;
                    var i = {"included-cdata": {pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, inside: n}};
                    i["language-" + t] = {pattern: /[\s\S]+/, inside: a.languages[t]};
                    var s = {};
                    s[e] = {
                        pattern: RegExp(/(<__[^>]*>)(?:<!\[CDATA\[(?:[^\]]|\](?!\]>))*\]\]>|(?!<!\[CDATA\[)[\s\S])*?(?=<\/__>)/.source.replace(/__/g, (function () {
                            return e
                        })), "i"), lookbehind: !0, greedy: !0, inside: i
                    }, a.languages.insertBefore("markup", "cdata", s)
                }
            }), Object.defineProperty(a.languages.markup.tag, "addAttribute", {
                value: function (e, t) {
                    a.languages.markup.tag.inside["special-attr"].push({
                        pattern: RegExp(/(^|["'\s])/.source + "(?:" + e + ")" + /\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))/.source, "i"),
                        lookbehind: !0,
                        inside: {
                            "attr-name": /^[^\s=]+/,
                            "attr-value": {
                                pattern: /=[\s\S]+/,
                                inside: {
                                    value: {
                                        pattern: /(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,
                                        lookbehind: !0,
                                        alias: [t, "language-" + t],
                                        inside: a.languages[t]
                                    }, punctuation: [{pattern: /^=/, alias: "attr-equals"}, /"|'/]
                                }
                            }
                        }
                    })
                }
            }), a.languages.html = a.languages.markup, a.languages.mathml = a.languages.markup, a.languages.svg = a.languages.markup, a.languages.xml = a.languages.extend("markup", {}), a.languages.ssml = a.languages.xml, a.languages.atom = a.languages.xml, a.languages.rss = a.languages.xml, function (e) {
                var t = /(?:"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n])*')/;
                e.languages.css = {
                    comment: /\/\*[\s\S]*?\*\//,
                    atrule: {
                        pattern: /@[\w-](?:[^;{\s]|\s+(?![\s{]))*(?:;|(?=\s*\{))/,
                        inside: {
                            rule: /^@[\w-]+/,
                            "selector-function-argument": {
                                pattern: /(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/,
                                lookbehind: !0,
                                alias: "selector"
                            },
                            keyword: {pattern: /(^|[^\w-])(?:and|not|only|or)(?![\w-])/, lookbehind: !0}
                        }
                    },
                    url: {
                        pattern: RegExp("\\burl\\((?:" + t.source + "|" + /(?:[^\\\r\n()"']|\\[\s\S])*/.source + ")\\)", "i"),
                        greedy: !0,
                        inside: {
                            function: /^url/i,
                            punctuation: /^\(|\)$/,
                            string: {pattern: RegExp("^" + t.source + "$"), alias: "url"}
                        }
                    },
                    selector: {
                        pattern: RegExp("(^|[{}\\s])[^{}\\s](?:[^{};\"'\\s]|\\s+(?![\\s{])|" + t.source + ")*(?=\\s*\\{)"),
                        lookbehind: !0
                    },
                    string: {pattern: t, greedy: !0},
                    property: {
                        pattern: /(^|[^-\w\xA0-\uFFFF])(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i,
                        lookbehind: !0
                    },
                    important: /!important\b/i,
                    function: {pattern: /(^|[^-a-z0-9])[-a-z0-9]+(?=\()/i, lookbehind: !0},
                    punctuation: /[(){};:,]/
                }, e.languages.css.atrule.inside.rest = e.languages.css;
                var n = e.languages.markup;
                n && (n.tag.addInlined("style", "css"), n.tag.addAttribute("style", "css"))
            }(a), a.languages.clike = {
                comment: [{
                    pattern: /(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,
                    lookbehind: !0,
                    greedy: !0
                }, {pattern: /(^|[^\\:])\/\/.*/, lookbehind: !0, greedy: !0}],
                string: {pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/, greedy: !0},
                "class-name": {
                    pattern: /(\b(?:class|interface|extends|implements|trait|instanceof|new)\s+|\bcatch\s+\()[\w.\\]+/i,
                    lookbehind: !0,
                    inside: {punctuation: /[.\\]/}
                },
                keyword: /\b(?:if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,
                boolean: /\b(?:true|false)\b/,
                function: /\b\w+(?=\()/,
                number: /\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,
                operator: /[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/,
                punctuation: /[{}[\];(),.:]/
            }, a.languages.javascript = a.languages.extend("clike", {
                "class-name": [a.languages.clike["class-name"], {
                    pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:prototype|constructor))/,
                    lookbehind: !0
                }],
                keyword: [{
                    pattern: /((?:^|\})\s*)catch\b/,
                    lookbehind: !0
                }, {
                    pattern: /(^|[^.]|\.\.\.\s*)\b(?:as|assert(?=\s*\{)|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/,
                    lookbehind: !0
                }],
                function: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,
                number: /\b(?:(?:0[xX](?:[\dA-Fa-f](?:_[\dA-Fa-f])?)+|0[bB](?:[01](?:_[01])?)+|0[oO](?:[0-7](?:_[0-7])?)+)n?|(?:\d(?:_\d)?)+n|NaN|Infinity)\b|(?:\b(?:\d(?:_\d)?)+\.?(?:\d(?:_\d)?)*|\B\.(?:\d(?:_\d)?)+)(?:[Ee][+-]?(?:\d(?:_\d)?)+)?/,
                operator: /--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/
            }), a.languages.javascript["class-name"][0].pattern = /(\b(?:class|interface|extends|implements|instanceof|new)\s+)[\w.\\]+/, a.languages.insertBefore("javascript", "keyword", {
                regex: {
                    pattern: /((?:^|[^$\w\xA0-\uFFFF."'\])\s]|\b(?:return|yield))\s*)\/(?:\[(?:[^\]\\\r\n]|\\.)*\]|\\.|[^/\\\[\r\n])+\/[dgimyus]{0,7}(?=(?:\s|\/\*(?:[^*]|\*(?!\/))*\*\/)*(?:$|[\r\n,.;:})\]]|\/\/))/,
                    lookbehind: !0,
                    greedy: !0,
                    inside: {
                        "regex-source": {
                            pattern: /^(\/)[\s\S]+(?=\/[a-z]*$)/,
                            lookbehind: !0,
                            alias: "language-regex",
                            inside: a.languages.regex
                        }, "regex-delimiter": /^\/|\/$/, "regex-flags": /^[a-z]+$/
                    }
                },
                "function-variable": {
                    pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/,
                    alias: "function"
                },
                parameter: [{
                    pattern: /(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/,
                    lookbehind: !0,
                    inside: a.languages.javascript
                }, {
                    pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$a-z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i,
                    lookbehind: !0,
                    inside: a.languages.javascript
                }, {
                    pattern: /(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/,
                    lookbehind: !0,
                    inside: a.languages.javascript
                }, {
                    pattern: /((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/,
                    lookbehind: !0,
                    inside: a.languages.javascript
                }],
                constant: /\b[A-Z](?:[A-Z_]|\dx?)*\b/
            }), a.languages.insertBefore("javascript", "string", {
                hashbang: {
                    pattern: /^#!.*/,
                    greedy: !0,
                    alias: "comment"
                },
                "template-string": {
                    pattern: /`(?:\\[\s\S]|\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}|(?!\$\{)[^\\`])*`/,
                    greedy: !0,
                    inside: {
                        "template-punctuation": {pattern: /^`|`$/, alias: "string"},
                        interpolation: {
                            pattern: /((?:^|[^\\])(?:\\{2})*)\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}/,
                            lookbehind: !0,
                            inside: {
                                "interpolation-punctuation": {pattern: /^\$\{|\}$/, alias: "punctuation"},
                                rest: a.languages.javascript
                            }
                        },
                        string: /[\s\S]+/
                    }
                }
            }), a.languages.markup && (a.languages.markup.tag.addInlined("script", "javascript"), a.languages.markup.tag.addAttribute(/on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)/.source, "javascript")), a.languages.js = a.languages.javascript, function () {
                if (void 0 !== a && "undefined" != typeof document) {
                    Element.prototype.matches || (Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector);
                    var e = {
                            js: "javascript",
                            py: "python",
                            rb: "ruby",
                            ps1: "powershell",
                            psm1: "powershell",
                            sh: "bash",
                            bat: "batch",
                            h: "c",
                            tex: "latex"
                        }, t = "data-src-status",
                        n = 'pre[data-src]:not([data-src-status="loaded"]):not([data-src-status="loading"])',
                        i = /\blang(?:uage)?-([\w-]+)\b/i;
                    a.hooks.add("before-highlightall", (function (e) {
                        e.selector += ", " + n
                    })), a.hooks.add("before-sanity-check", (function (i) {
                        var s = i.element;
                        if (s.matches(n)) {
                            i.code = "", s.setAttribute(t, "loading");
                            var o = s.appendChild(document.createElement("CODE"));
                            o.textContent = "Loading…";
                            var l = s.getAttribute("data-src"), d = i.language;
                            if ("none" === d) {
                                var c = (/\.(\w+)$/.exec(l) || [, "none"])[1];
                                d = e[c] || c
                            }
                            r(o, d), r(s, d);
                            var u = a.plugins.autoloader;
                            u && u.loadLanguages(d);
                            var p = new XMLHttpRequest;
                            p.open("GET", l, !0), p.onreadystatechange = function () {
                                4 == p.readyState && (p.status < 400 && p.responseText ? (s.setAttribute(t, "loaded"), o.textContent = p.responseText, a.highlightElement(o)) : (s.setAttribute(t, "failed"), p.status >= 400 ? o.textContent = "✖ Error " + p.status + " while fetching file: " + p.statusText : o.textContent = "✖ Error: File does not exist or is empty"))
                            }, p.send(null)
                        }
                    })), a.plugins.fileHighlight = {
                        highlight: function (e) {
                            for (var t, i = (e || document).querySelectorAll(n), s = 0; t = i[s++];) a.highlightElement(t)
                        }
                    };
                    var s = !1;
                    a.fileHighlight = function () {
                        s || (console.warn("Prism.fileHighlight is deprecated. Use `Prism.plugins.fileHighlight.highlight` instead."), s = !0), a.plugins.fileHighlight.highlight.apply(this, arguments)
                    }
                }

                function r(e, t) {
                    var n = e.className;
                    n = n.replace(i, " ") + " language-" + t, e.className = n.replace(/\s+/g, " ").trim()
                }
            }()
        }, 379: e => {
            "use strict";
            var t = [];

            function n(e) {
                for (var n = -1, a = 0; a < t.length; a++) if (t[a].identifier === e) {
                    n = a;
                    break
                }
                return n
            }

            function a(e, a) {
                for (var s = {}, r = [], o = 0; o < e.length; o++) {
                    var l = e[o], d = a.base ? l[0] + a.base : l[0], c = s[d] || 0, u = "".concat(d, " ").concat(c);
                    s[d] = c + 1;
                    var p = n(u), g = {css: l[1], media: l[2], sourceMap: l[3], supports: l[4], layer: l[5]};
                    if (-1 !== p) t[p].references++, t[p].updater(g); else {
                        var h = i(g, a);
                        a.byIndex = o, t.splice(o, 0, {identifier: u, updater: h, references: 1})
                    }
                    r.push(u)
                }
                return r
            }

            function i(e, t) {
                var n = t.domAPI(t);
                return n.update(e), function (t) {
                    if (t) {
                        if (t.css === e.css && t.media === e.media && t.sourceMap === e.sourceMap && t.supports === e.supports && t.layer === e.layer) return;
                        n.update(e = t)
                    } else n.remove()
                }
            }

            e.exports = function (e, i) {
                var s = a(e = e || [], i = i || {});
                return function (e) {
                    e = e || [];
                    for (var r = 0; r < s.length; r++) {
                        var o = n(s[r]);
                        t[o].references--
                    }
                    for (var l = a(e, i), d = 0; d < s.length; d++) {
                        var c = n(s[d]);
                        0 === t[c].references && (t[c].updater(), t.splice(c, 1))
                    }
                    s = l
                }
            }
        }, 569: e => {
            "use strict";
            var t = {};
            e.exports = function (e, n) {
                var a = function (e) {
                    if (void 0 === t[e]) {
                        var n = document.querySelector(e);
                        if (window.HTMLIFrameElement && n instanceof window.HTMLIFrameElement) try {
                            n = n.contentDocument.head
                        } catch (e) {
                            n = null
                        }
                        t[e] = n
                    }
                    return t[e]
                }(e);
                if (!a) throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
                a.appendChild(n)
            }
        }, 216: e => {
            "use strict";
            e.exports = function (e) {
                var t = document.createElement("style");
                return e.setAttributes(t, e.attributes), e.insert(t, e.options), t
            }
        }, 565: (e, t, n) => {
            "use strict";
            e.exports = function (e) {
                var t = n.nc;
                t && e.setAttribute("nonce", t)
            }
        }, 795: e => {
            "use strict";
            e.exports = function (e) {
                var t = e.insertStyleElement(e);
                return {
                    update: function (n) {
                        !function (e, t, n) {
                            var a = "";
                            n.supports && (a += "@supports (".concat(n.supports, ") {")), n.media && (a += "@media ".concat(n.media, " {"));
                            var i = void 0 !== n.layer;
                            i && (a += "@layer".concat(n.layer.length > 0 ? " ".concat(n.layer) : "", " {")), a += n.css, i && (a += "}"), n.media && (a += "}"), n.supports && (a += "}");
                            var s = n.sourceMap;
                            s && "undefined" != typeof btoa && (a += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(s)))), " */")), t.styleTagTransform(a, e, t.options)
                        }(t, e, n)
                    }, remove: function () {
                        !function (e) {
                            if (null === e.parentNode) return !1;
                            e.parentNode.removeChild(e)
                        }(t)
                    }
                }
            }
        }, 589: e => {
            "use strict";
            e.exports = function (e, t) {
                if (t.styleSheet) t.styleSheet.cssText = e; else {
                    for (; t.firstChild;) t.removeChild(t.firstChild);
                    t.appendChild(document.createTextNode(e))
                }
            }
        }, 497: e => {
            e.exports = '<svg width="14" height="14" viewBox="0 -1 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M3.177 6.852c.205.253.347.572.427.954.078.372.117.844.117 1.417 0 .418.01.725.03.92.02.18.057.314.107.396.046.075.093.117.14.134.075.027.218.056.42.083a.855.855 0 0 1 .56.297c.145.167.215.38.215.636 0 .612-.432.934-1.216.934-.457 0-.87-.087-1.233-.262a1.995 1.995 0 0 1-.853-.751 2.09 2.09 0 0 1-.305-1.097c-.014-.648-.029-1.168-.043-1.56-.013-.383-.034-.631-.06-.733-.064-.263-.158-.455-.276-.578a2.163 2.163 0 0 0-.505-.376c-.238-.134-.41-.256-.519-.371C.058 6.76 0 6.567 0 6.315c0-.37.166-.657.493-.846.329-.186.56-.342.693-.466a.942.942 0 0 0 .26-.447c.056-.2.088-.42.097-.658.01-.25.024-.85.043-1.802.015-.629.239-1.14.672-1.522C2.691.19 3.268 0 3.977 0c.783 0 1.216.317 1.216.921 0 .264-.069.48-.211.643a.858.858 0 0 1-.563.29c-.249.03-.417.076-.498.126-.062.04-.112.134-.139.291-.031.187-.052.562-.061 1.119a8.828 8.828 0 0 1-.112 1.378 2.24 2.24 0 0 1-.404.963c-.159.212-.373.406-.64.583.25.163.454.342.612.538zm7.34 0c.157-.196.362-.375.612-.538a2.544 2.544 0 0 1-.641-.583 2.24 2.24 0 0 1-.404-.963 8.828 8.828 0 0 1-.112-1.378c-.009-.557-.03-.932-.061-1.119-.027-.157-.077-.251-.14-.29-.08-.051-.248-.096-.496-.127a.858.858 0 0 1-.564-.29C8.57 1.401 8.5 1.185 8.5.921 8.5.317 8.933 0 9.716 0c.71 0 1.286.19 1.72.574.432.382.656.893.671 1.522.02.952.033 1.553.043 1.802.009.238.041.458.097.658a.942.942 0 0 0 .26.447c.133.124.364.28.693.466a.926.926 0 0 1 .493.846c0 .252-.058.446-.183.58-.109.115-.281.237-.52.371-.21.118-.377.244-.504.376-.118.123-.212.315-.277.578-.025.102-.045.35-.06.733-.013.392-.027.912-.042 1.56a2.09 2.09 0 0 1-.305 1.097c-.2.323-.486.574-.853.75a2.811 2.811 0 0 1-1.233.263c-.784 0-1.216-.322-1.216-.934 0-.256.07-.47.214-.636a.855.855 0 0 1 .562-.297c.201-.027.344-.056.418-.083.048-.017.096-.06.14-.134a.996.996 0 0 0 .107-.396c.02-.195.031-.502.031-.92 0-.573.039-1.045.117-1.417.08-.382.222-.701.427-.954z"></path></svg>'
        }
    }, t = {};

    function n(a) {
        var i = t[a];
        if (void 0 !== i) return i.exports;
        var s = t[a] = {id: a, exports: {}};
        return e[a](s, s.exports, n), s.exports
    }

    n.n = e => {
        var t = e && e.__esModule ? () => e.default : () => e;
        return n.d(t, {a: t}), t
    }, n.d = (e, t) => {
        for (var a in t) n.o(t, a) && !n.o(e, a) && Object.defineProperty(e, a, {enumerable: !0, get: t[a]})
    }, n.g = function () {
        if ("object" == typeof globalThis) return globalThis;
        try {
            return this || new Function("return this")()
        } catch (e) {
            if ("object" == typeof window) return window
        }
    }(), n.o = (e, t) => Object.prototype.hasOwnProperty.call(e, t);
    var a = {};
    return (() => {
        "use strict";
        n.d(a, {default: () => $});
        var e = n(379), t = n.n(e), i = n(795), s = n.n(i), r = n(569), o = n.n(r), l = n(565), d = n.n(l), c = n(216),
            u = n.n(c), p = n(589), g = n.n(p), h = n(738), f = {};
        f.styleTagTransform = g(), f.setAttributes = d(), f.insert = o().bind(null, "head"), f.domAPI = s(), f.insertStyleElement = u(), t()(h.Z, f), h.Z && h.Z.locals && h.Z.locals;
        var m, b = n(497), y = n.n(b), v = n(660), k = n.n(v),
            x = (n(689), n(335), n(64), n(277), n(366), n(874), "#fff"),
            w = '"SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace',
            S = "\n  .codeflask {\n    position: absolute;\n    width: 100%;\n    height: 100%;\n    overflow: hidden;\n  }\n\n  .codeflask, .codeflask * {\n    box-sizing: border-box;\n  }\n\n  .codeflask__pre {\n    pointer-events: none;\n    z-index: 3;\n    overflow: hidden;\n  }\n\n  .codeflask__textarea {\n    background: none;\n    border: none;\n    color: " + (m = "caret-color", "#000", (("undefined" != typeof CSS ? CSS.supports(m, "#000") : "undefined" != typeof document && function (e) {
                return (e = e.split("-").filter((function (e) {
                    return !!e
                })).map((function (e) {
                    return e[0].toUpperCase() + e.substr(1)
                })).join(""))[0].toLowerCase() + e.substr(1)
            }(m) in document.body.style) ? x : "#ccc") + ";\n    z-index: 1;\n    resize: none;\n    font-family: ") + w + ";\n    -webkit-appearance: pre;\n    caret-color: #111;\n    z-index: 2;\n    width: 100%;\n    height: 100%;\n  }\n\n  .codeflask--has-line-numbers .codeflask__textarea {\n    width: calc(100% - 40px);\n  }\n\n  .codeflask__code {\n    display: block;\n    font-family: " + w + ";\n    overflow: hidden;\n  }\n\n  .codeflask__flatten {\n    padding: 10px;\n    font-size: 13px;\n    line-height: 20px;\n    white-space: pre;\n    position: absolute;\n    top: 0;\n    left: 0;\n    overflow: auto;\n    margin: 0 !important;\n    outline: none;\n    text-align: left;\n  }\n\n  .codeflask--has-line-numbers .codeflask__flatten {\n    width: calc(100% - 40px);\n    left: 40px;\n  }\n\n  .codeflask__line-highlight {\n    position: absolute;\n    top: 10px;\n    left: 0;\n    width: 100%;\n    height: 20px;\n    background: rgba(0,0,0,0.1);\n    z-index: 1;\n  }\n\n  .codeflask__lines {\n    padding: 10px 4px;\n    font-size: 12px;\n    line-height: 20px;\n    font-family: 'Cousine', monospace;\n    position: absolute;\n    left: 0;\n    top: 0;\n    width: 40px;\n    height: 100%;\n    text-align: right;\n    color: #999;\n    z-index: 2;\n  }\n\n  .codeflask__lines__line {\n    display: block;\n  }\n\n  .codeflask.codeflask--has-line-numbers {\n    padding-left: 40px;\n  }\n\n  .codeflask.codeflask--has-line-numbers:before {\n    content: '';\n    position: absolute;\n    left: 0;\n    top: 0;\n    width: 40px;\n    height: 100%;\n    background: #eee;\n    z-index: 1;\n  }\n";

        function E(e, t, n) {
            var a = t || "codeflask-style", i = n || document.head;
            if (!e) return !1;
            if (document.getElementById(a)) return !0;
            var s = document.createElement("style");
            return s.innerHTML = e, s.id = a, i.appendChild(s), !0
        }

        var A = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;",
            "/": "&#x2F;",
            "`": "&#x60;",
            "=": "&#x3D;"
        };

        function _(e) {
            return String(e).replace(/[&<>"'`=/]/g, (function (e) {
                return A[e]
            }))
        }

        var T = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : void 0 !== n.g ? n.g : "undefined" != typeof self ? self : {},
            F = function (e, t) {
                return function (e) {
                    var t = function (e) {
                        var t = /\blang(?:uage)?-([\w-]+)\b/i, n = 0, a = {
                            manual: e.Prism && e.Prism.manual,
                            disableWorkerMessageHandler: e.Prism && e.Prism.disableWorkerMessageHandler,
                            util: {
                                encode: function (e) {
                                    return e instanceof i ? new i(e.type, a.util.encode(e.content), e.alias) : Array.isArray(e) ? e.map(a.util.encode) : e.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/\u00a0/g, " ")
                                }, type: function (e) {
                                    return Object.prototype.toString.call(e).slice(8, -1)
                                }, objId: function (e) {
                                    return e.__id || Object.defineProperty(e, "__id", {value: ++n}), e.__id
                                }, clone: function e(t, n) {
                                    var i, s, r = a.util.type(t);
                                    switch (n = n || {}, r) {
                                        case"Object":
                                            if (s = a.util.objId(t), n[s]) return n[s];
                                            for (var o in i = {}, n[s] = i, t) t.hasOwnProperty(o) && (i[o] = e(t[o], n));
                                            return i;
                                        case"Array":
                                            return s = a.util.objId(t), n[s] ? n[s] : (i = [], n[s] = i, t.forEach((function (t, a) {
                                                i[a] = e(t, n)
                                            })), i);
                                        default:
                                            return t
                                    }
                                }
                            },
                            languages: {
                                extend: function (e, t) {
                                    var n = a.util.clone(a.languages[e]);
                                    for (var i in t) n[i] = t[i];
                                    return n
                                }, insertBefore: function (e, t, n, i) {
                                    var s = (i = i || a.languages)[e], r = {};
                                    for (var o in s) if (s.hasOwnProperty(o)) {
                                        if (o == t) for (var l in n) n.hasOwnProperty(l) && (r[l] = n[l]);
                                        n.hasOwnProperty(o) || (r[o] = s[o])
                                    }
                                    var d = i[e];
                                    return i[e] = r, a.languages.DFS(a.languages, (function (t, n) {
                                        n === d && t != e && (this[t] = r)
                                    })), r
                                }, DFS: function e(t, n, i, s) {
                                    s = s || {};
                                    var r = a.util.objId;
                                    for (var o in t) if (t.hasOwnProperty(o)) {
                                        n.call(t, o, t[o], i || o);
                                        var l = t[o], d = a.util.type(l);
                                        "Object" !== d || s[r(l)] ? "Array" !== d || s[r(l)] || (s[r(l)] = !0, e(l, n, o, s)) : (s[r(l)] = !0, e(l, n, null, s))
                                    }
                                }
                            },
                            plugins: {},
                            highlightAll: function (e, t) {
                                a.highlightAllUnder(document, e, t)
                            },
                            highlightAllUnder: function (e, t, n) {
                                var i = {
                                    callback: n,
                                    selector: 'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'
                                };
                                a.hooks.run("before-highlightall", i);
                                for (var s, r = i.elements || e.querySelectorAll(i.selector), o = 0; s = r[o++];) a.highlightElement(s, !0 === t, i.callback)
                            },
                            highlightElement: function (n, i, s) {
                                for (var r, o, l = n; l && !t.test(l.className);) l = l.parentNode;
                                l && (r = (l.className.match(t) || [, ""])[1].toLowerCase(), o = a.languages[r]), n.className = n.className.replace(t, "").replace(/\s+/g, " ") + " language-" + r, n.parentNode && (l = n.parentNode, /pre/i.test(l.nodeName) && (l.className = l.className.replace(t, "").replace(/\s+/g, " ") + " language-" + r));
                                var d = {element: n, language: r, grammar: o, code: n.textContent}, c = function (e) {
                                    d.highlightedCode = e, a.hooks.run("before-insert", d), d.element.innerHTML = d.highlightedCode, a.hooks.run("after-highlight", d), a.hooks.run("complete", d), s && s.call(d.element)
                                };
                                if (a.hooks.run("before-sanity-check", d), d.code) if (a.hooks.run("before-highlight", d), d.grammar) if (i && e.Worker) {
                                    var u = new Worker(a.filename);
                                    u.onmessage = function (e) {
                                        c(e.data)
                                    }, u.postMessage(JSON.stringify({
                                        language: d.language,
                                        code: d.code,
                                        immediateClose: !0
                                    }))
                                } else c(a.highlight(d.code, d.grammar, d.language)); else c(a.util.encode(d.code)); else a.hooks.run("complete", d)
                            },
                            highlight: function (e, t, n) {
                                var s = {code: e, grammar: t, language: n};
                                return a.hooks.run("before-tokenize", s), s.tokens = a.tokenize(s.code, s.grammar), a.hooks.run("after-tokenize", s), i.stringify(a.util.encode(s.tokens), s.language)
                            },
                            matchGrammar: function (e, t, n, s, r, o, l) {
                                for (var d in n) if (n.hasOwnProperty(d) && n[d]) {
                                    if (d == l) return;
                                    var c = n[d];
                                    c = "Array" === a.util.type(c) ? c : [c];
                                    for (var u = 0; u < c.length; ++u) {
                                        var p = c[u], g = p.inside, h = !!p.lookbehind, f = !!p.greedy, m = 0,
                                            b = p.alias;
                                        if (f && !p.pattern.global) {
                                            var y = p.pattern.toString().match(/[imuy]*$/)[0];
                                            p.pattern = RegExp(p.pattern.source, y + "g")
                                        }
                                        p = p.pattern || p;
                                        for (var v = s, k = r; v < t.length; k += t[v].length, ++v) {
                                            var x = t[v];
                                            if (t.length > e.length) return;
                                            if (!(x instanceof i)) {
                                                if (f && v != t.length - 1) {
                                                    if (p.lastIndex = k, !(T = p.exec(e))) break;
                                                    for (var w = T.index + (h ? T[1].length : 0), S = T.index + T[0].length, E = v, A = k, _ = t.length; E < _ && (A < S || !t[E].type && !t[E - 1].greedy); ++E) w >= (A += t[E].length) && (++v, k = A);
                                                    if (t[v] instanceof i) continue;
                                                    F = E - v, x = e.slice(k, A), T.index -= k
                                                } else {
                                                    p.lastIndex = 0;
                                                    var T = p.exec(x), F = 1
                                                }
                                                if (T) {
                                                    h && (m = T[1] ? T[1].length : 0), S = (w = T.index + m) + (T = T[0].slice(m)).length;
                                                    var C = x.slice(0, w), L = x.slice(S), O = [v, F];
                                                    C && (++v, k += C.length, O.push(C));
                                                    var N = new i(d, g ? a.tokenize(T, g) : T, b, T, f);
                                                    if (O.push(N), L && O.push(L), Array.prototype.splice.apply(t, O), 1 != F && a.matchGrammar(e, t, n, v, k, !0, d), o) break
                                                } else if (o) break
                                            }
                                        }
                                    }
                                }
                            },
                            tokenize: function (e, t) {
                                var n = [e], i = t.rest;
                                if (i) {
                                    for (var s in i) t[s] = i[s];
                                    delete t.rest
                                }
                                return a.matchGrammar(e, n, t, 0, 0, !1), n
                            },
                            hooks: {
                                all: {}, add: function (e, t) {
                                    var n = a.hooks.all;
                                    n[e] = n[e] || [], n[e].push(t)
                                }, run: function (e, t) {
                                    var n = a.hooks.all[e];
                                    if (n && n.length) for (var i, s = 0; i = n[s++];) i(t)
                                }
                            },
                            Token: i
                        };

                        function i(e, t, n, a, i) {
                            this.type = e, this.content = t, this.alias = n, this.length = 0 | (a || "").length, this.greedy = !!i
                        }

                        if (e.Prism = a, i.stringify = function (e, t, n) {
                            if ("string" == typeof e) return e;
                            if (Array.isArray(e)) return e.map((function (n) {
                                return i.stringify(n, t, e)
                            })).join("");
                            var s = {
                                type: e.type,
                                content: i.stringify(e.content, t, n),
                                tag: "span",
                                classes: ["token", e.type],
                                attributes: {},
                                language: t,
                                parent: n
                            };
                            if (e.alias) {
                                var r = Array.isArray(e.alias) ? e.alias : [e.alias];
                                Array.prototype.push.apply(s.classes, r)
                            }
                            a.hooks.run("wrap", s);
                            var o = Object.keys(s.attributes).map((function (e) {
                                return e + '="' + (s.attributes[e] || "").replace(/"/g, "&quot;") + '"'
                            })).join(" ");
                            return "<" + s.tag + ' class="' + s.classes.join(" ") + '"' + (o ? " " + o : "") + ">" + s.content + "</" + s.tag + ">"
                        }, !e.document) return e.addEventListener ? (a.disableWorkerMessageHandler || e.addEventListener("message", (function (t) {
                            var n = JSON.parse(t.data), i = n.language, s = n.code, r = n.immediateClose;
                            e.postMessage(a.highlight(s, a.languages[i], i)), r && e.close()
                        }), !1), a) : a;
                        var s = document.currentScript || [].slice.call(document.getElementsByTagName("script")).pop();
                        return s && (a.filename = s.src, a.manual || s.hasAttribute("data-manual") || ("loading" !== document.readyState ? window.requestAnimationFrame ? window.requestAnimationFrame(a.highlightAll) : window.setTimeout(a.highlightAll, 16) : document.addEventListener("DOMContentLoaded", a.highlightAll))), a
                    }("undefined" != typeof window ? window : "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope ? self : {});
                    e.exports && (e.exports = t), void 0 !== T && (T.Prism = t), t.languages.markup = {
                        comment: /<!--[\s\S]*?-->/,
                        prolog: /<\?[\s\S]+?\?>/,
                        doctype: /<!DOCTYPE[\s\S]+?>/i,
                        cdata: /<!\[CDATA\[[\s\S]*?]]>/i,
                        tag: {
                            pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/i,
                            greedy: !0,
                            inside: {
                                tag: {
                                    pattern: /^<\/?[^\s>\/]+/i,
                                    inside: {punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/}
                                },
                                "attr-value": {
                                    pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/i,
                                    inside: {punctuation: [/^=/, {pattern: /^(\s*)["']|["']$/, lookbehind: !0}]}
                                },
                                punctuation: /\/?>/,
                                "attr-name": {pattern: /[^\s>\/]+/, inside: {namespace: /^[^\s>\/:]+:/}}
                            }
                        },
                        entity: /&#?[\da-z]{1,8};/i
                    }, t.languages.markup.tag.inside["attr-value"].inside.entity = t.languages.markup.entity, t.hooks.add("wrap", (function (e) {
                        "entity" === e.type && (e.attributes.title = e.content.replace(/&amp;/, "&"))
                    })), Object.defineProperty(t.languages.markup.tag, "addInlined", {
                        value: function (e, n) {
                            var a = {};
                            a["language-" + n] = {
                                pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,
                                lookbehind: !0,
                                inside: t.languages[n]
                            }, a.cdata = /^<!\[CDATA\[|\]\]>$/i;
                            var i = {"included-cdata": {pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, inside: a}};
                            i["language-" + n] = {pattern: /[\s\S]+/, inside: t.languages[n]};
                            var s = {};
                            s[e] = {
                                pattern: RegExp(/(<__[\s\S]*?>)(?:<!\[CDATA\[[\s\S]*?\]\]>\s*|[\s\S])*?(?=<\/__>)/.source.replace(/__/g, e), "i"),
                                lookbehind: !0,
                                greedy: !0,
                                inside: i
                            }, t.languages.insertBefore("markup", "cdata", s)
                        }
                    }), t.languages.xml = t.languages.extend("markup", {}), t.languages.html = t.languages.markup, t.languages.mathml = t.languages.markup, t.languages.svg = t.languages.markup, function (e) {
                        var t = /("|')(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/;
                        e.languages.css = {
                            comment: /\/\*[\s\S]*?\*\//,
                            atrule: {pattern: /@[\w-]+?[\s\S]*?(?:;|(?=\s*\{))/i, inside: {rule: /@[\w-]+/}},
                            url: RegExp("url\\((?:" + t.source + "|.*?)\\)", "i"),
                            selector: RegExp("[^{}\\s](?:[^{};\"']|" + t.source + ")*?(?=\\s*\\{)"),
                            string: {pattern: t, greedy: !0},
                            property: /[-_a-z\xA0-\uFFFF][-\w\xA0-\uFFFF]*(?=\s*:)/i,
                            important: /!important\b/i,
                            function: /[-a-z0-9]+(?=\()/i,
                            punctuation: /[(){};:,]/
                        }, e.languages.css.atrule.inside.rest = e.languages.css;
                        var n = e.languages.markup;
                        n && (n.tag.addInlined("style", "css"), e.languages.insertBefore("inside", "attr-value", {
                            "style-attr": {
                                pattern: /\s*style=("|')(?:\\[\s\S]|(?!\1)[^\\])*\1/i,
                                inside: {
                                    "attr-name": {pattern: /^\s*style/i, inside: n.tag.inside},
                                    punctuation: /^\s*=\s*['"]|['"]\s*$/,
                                    "attr-value": {pattern: /.+/i, inside: e.languages.css}
                                },
                                alias: "language-css"
                            }
                        }, n.tag))
                    }(t), t.languages.clike = {
                        comment: [{
                            pattern: /(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,
                            lookbehind: !0
                        }, {pattern: /(^|[^\\:])\/\/.*/, lookbehind: !0, greedy: !0}],
                        string: {pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/, greedy: !0},
                        "class-name": {
                            pattern: /((?:\b(?:class|interface|extends|implements|trait|instanceof|new)\s+)|(?:catch\s+\())[\w.\\]+/i,
                            lookbehind: !0,
                            inside: {punctuation: /[.\\]/}
                        },
                        keyword: /\b(?:if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,
                        boolean: /\b(?:true|false)\b/,
                        function: /\w+(?=\()/,
                        number: /\b0x[\da-f]+\b|(?:\b\d+\.?\d*|\B\.\d+)(?:e[+-]?\d+)?/i,
                        operator: /--?|\+\+?|!=?=?|<=?|>=?|==?=?|&&?|\|\|?|\?|\*|\/|~|\^|%/,
                        punctuation: /[{}[\];(),.:]/
                    }, t.languages.javascript = t.languages.extend("clike", {
                        "class-name": [t.languages.clike["class-name"], {
                            pattern: /(^|[^$\w\xA0-\uFFFF])[_$A-Z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\.(?:prototype|constructor))/,
                            lookbehind: !0
                        }],
                        keyword: [{
                            pattern: /((?:^|})\s*)(?:catch|finally)\b/,
                            lookbehind: !0
                        }, {
                            pattern: /(^|[^.])\b(?:as|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/,
                            lookbehind: !0
                        }],
                        number: /\b(?:(?:0[xX][\dA-Fa-f]+|0[bB][01]+|0[oO][0-7]+)n?|\d+n|NaN|Infinity)\b|(?:\b\d+\.?\d*|\B\.\d+)(?:[Ee][+-]?\d+)?/,
                        function: /[_$a-zA-Z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,
                        operator: /-[-=]?|\+[+=]?|!=?=?|<<?=?|>>?>?=?|=(?:==?|>)?|&[&=]?|\|[|=]?|\*\*?=?|\/=?|~|\^=?|%=?|\?|\.{3}/
                    }), t.languages.javascript["class-name"][0].pattern = /(\b(?:class|interface|extends|implements|instanceof|new)\s+)[\w.\\]+/, t.languages.insertBefore("javascript", "keyword", {
                        regex: {
                            pattern: /((?:^|[^$\w\xA0-\uFFFF."'\])\s])\s*)\/(\[(?:[^\]\\\r\n]|\\.)*]|\\.|[^/\\\[\r\n])+\/[gimyu]{0,5}(?=\s*($|[\r\n,.;})\]]))/,
                            lookbehind: !0,
                            greedy: !0
                        },
                        "function-variable": {
                            pattern: /[_$a-zA-Z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|[_$a-zA-Z\xA0-\uFFFF][$\w\xA0-\uFFFF]*)\s*=>))/,
                            alias: "function"
                        },
                        parameter: [{
                            pattern: /(function(?:\s+[_$A-Za-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*)?\s*\(\s*)(?!\s)(?:[^()]|\([^()]*\))+?(?=\s*\))/,
                            lookbehind: !0,
                            inside: t.languages.javascript
                        }, {
                            pattern: /[_$a-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\s*=>)/i,
                            inside: t.languages.javascript
                        }, {
                            pattern: /(\(\s*)(?!\s)(?:[^()]|\([^()]*\))+?(?=\s*\)\s*=>)/,
                            lookbehind: !0,
                            inside: t.languages.javascript
                        }, {
                            pattern: /((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:[_$A-Za-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*\s*)\(\s*)(?!\s)(?:[^()]|\([^()]*\))+?(?=\s*\)\s*\{)/,
                            lookbehind: !0,
                            inside: t.languages.javascript
                        }],
                        constant: /\b[A-Z](?:[A-Z_]|\dx?)*\b/
                    }), t.languages.insertBefore("javascript", "string", {
                        "template-string": {
                            pattern: /`(?:\\[\s\S]|\${[^}]+}|[^\\`])*`/,
                            greedy: !0,
                            inside: {
                                interpolation: {
                                    pattern: /\${[^}]+}/,
                                    inside: {
                                        "interpolation-punctuation": {pattern: /^\${|}$/, alias: "punctuation"},
                                        rest: t.languages.javascript
                                    }
                                }, string: /[\s\S]+/
                            }
                        }
                    }), t.languages.markup && t.languages.markup.tag.addInlined("script", "javascript"), t.languages.js = t.languages.javascript, "undefined" != typeof self && self.Prism && self.document && document.querySelector && (self.Prism.fileHighlight = function (e) {
                        e = e || document;
                        var n = {
                            js: "javascript",
                            py: "python",
                            rb: "ruby",
                            ps1: "powershell",
                            psm1: "powershell",
                            sh: "bash",
                            bat: "batch",
                            h: "c",
                            tex: "latex"
                        };
                        Array.prototype.slice.call(e.querySelectorAll("pre[data-src]")).forEach((function (e) {
                            if (!e.hasAttribute("data-src-loaded")) {
                                for (var a, i = e.getAttribute("data-src"), s = e, r = /\blang(?:uage)?-([\w-]+)\b/i; s && !r.test(s.className);) s = s.parentNode;
                                if (s && (a = (e.className.match(r) || [, ""])[1]), !a) {
                                    var o = (i.match(/\.(\w+)$/) || [, ""])[1];
                                    a = n[o] || o
                                }
                                var l = document.createElement("code");
                                l.className = "language-" + a, e.textContent = "", l.textContent = "Loading…", e.appendChild(l);
                                var d = new XMLHttpRequest;
                                d.open("GET", i, !0), d.onreadystatechange = function () {
                                    4 == d.readyState && (d.status < 400 && d.responseText ? (l.textContent = d.responseText, t.highlightElement(l), e.setAttribute("data-src-loaded", "")) : d.status >= 400 ? l.textContent = "✖ Error " + d.status + " while fetching file: " + d.statusText : l.textContent = "✖ Error: File does not exist or is empty")
                                }, d.send(null)
                            }
                        })), t.plugins.toolbar && t.plugins.toolbar.registerButton("download-file", (function (e) {
                            var t = e.element.parentNode;
                            if (t && /pre/i.test(t.nodeName) && t.hasAttribute("data-src") && t.hasAttribute("data-download-link")) {
                                var n = t.getAttribute("data-src"), a = document.createElement("a");
                                return a.textContent = t.getAttribute("data-download-link-label") || "Download", a.setAttribute("download", ""), a.href = n, a
                            }
                        }))
                    }, document.addEventListener("DOMContentLoaded", (function () {
                        self.Prism.fileHighlight()
                    })))
                }(t = {exports: {}}), t.exports
            }(), C = function (e, t) {
                if (!e) throw Error("CodeFlask expects a parameter which is Element or a String selector");
                if (!t) throw Error("CodeFlask expects an object containing options as second parameter");
                if (e.nodeType) this.editorRoot = e; else {
                    var n = document.querySelector(e);
                    n && (this.editorRoot = n)
                }
                this.opts = t, this.startEditor()
            };
        C.prototype.startEditor = function () {
            if (!E(S, null, this.opts.styleParent)) throw Error("Failed to inject CodeFlask CSS.");
            this.createWrapper(), this.createTextarea(), this.createPre(), this.createCode(), this.runOptions(), this.listenTextarea(), this.populateDefault(), this.updateCode(this.code)
        }, C.prototype.createWrapper = function () {
            this.code = this.editorRoot.innerHTML, this.editorRoot.innerHTML = "", this.elWrapper = this.createElement("div", this.editorRoot), this.elWrapper.classList.add("codeflask")
        }, C.prototype.createTextarea = function () {
            this.elTextarea = this.createElement("textarea", this.elWrapper), this.elTextarea.classList.add("codeflask__textarea", "codeflask__flatten")
        }, C.prototype.createPre = function () {
            this.elPre = this.createElement("pre", this.elWrapper), this.elPre.classList.add("codeflask__pre", "codeflask__flatten")
        }, C.prototype.createCode = function () {
            this.elCode = this.createElement("code", this.elPre), this.elCode.classList.add("codeflask__code", "language-" + (this.opts.language || "html"))
        }, C.prototype.createLineNumbers = function () {
            this.elLineNumbers = this.createElement("div", this.elWrapper), this.elLineNumbers.classList.add("codeflask__lines"), this.setLineNumber()
        }, C.prototype.createElement = function (e, t) {
            var n = document.createElement(e);
            return t.appendChild(n), n
        }, C.prototype.runOptions = function () {
            this.opts.rtl = this.opts.rtl || !1, this.opts.tabSize = this.opts.tabSize || 2, this.opts.enableAutocorrect = this.opts.enableAutocorrect || !1, this.opts.lineNumbers = this.opts.lineNumbers || !1, this.opts.defaultTheme = !1 !== this.opts.defaultTheme, this.opts.areaId = this.opts.areaId || null, this.opts.ariaLabelledby = this.opts.ariaLabelledby || null, this.opts.readonly = this.opts.readonly || null, "boolean" != typeof this.opts.handleTabs && (this.opts.handleTabs = !0), "boolean" != typeof this.opts.handleSelfClosingCharacters && (this.opts.handleSelfClosingCharacters = !0), "boolean" != typeof this.opts.handleNewLineIndentation && (this.opts.handleNewLineIndentation = !0), !0 === this.opts.rtl && (this.elTextarea.setAttribute("dir", "rtl"), this.elPre.setAttribute("dir", "rtl")), !1 === this.opts.enableAutocorrect && (this.elTextarea.setAttribute("spellcheck", "false"), this.elTextarea.setAttribute("autocapitalize", "off"), this.elTextarea.setAttribute("autocomplete", "off"), this.elTextarea.setAttribute("autocorrect", "off")), this.opts.lineNumbers && (this.elWrapper.classList.add("codeflask--has-line-numbers"), this.createLineNumbers()), this.opts.defaultTheme && E("\n.codeflask {\n  background: #fff;\n  color: #4f559c;\n}\n\n.codeflask .token.punctuation {\n  color: #4a4a4a;\n}\n\n.codeflask .token.keyword {\n  color: #8500ff;\n}\n\n.codeflask .token.operator {\n  color: #ff5598;\n}\n\n.codeflask .token.string {\n  color: #41ad8f;\n}\n\n.codeflask .token.comment {\n  color: #9badb7;\n}\n\n.codeflask .token.function {\n  color: #8500ff;\n}\n\n.codeflask .token.boolean {\n  color: #8500ff;\n}\n\n.codeflask .token.number {\n  color: #8500ff;\n}\n\n.codeflask .token.selector {\n  color: #8500ff;\n}\n\n.codeflask .token.property {\n  color: #8500ff;\n}\n\n.codeflask .token.tag {\n  color: #8500ff;\n}\n\n.codeflask .token.attr-value {\n  color: #8500ff;\n}\n", "theme-default", this.opts.styleParent), this.opts.areaId && this.elTextarea.setAttribute("id", this.opts.areaId), this.opts.ariaLabelledby && this.elTextarea.setAttribute("aria-labelledby", this.opts.ariaLabelledby), this.opts.readonly && this.enableReadonlyMode()
        }, C.prototype.updateLineNumbersCount = function () {
            for (var e = "", t = 1; t <= this.lineNumber; t++) e = e + '<span class="codeflask__lines__line">' + t + "</span>";
            this.elLineNumbers.innerHTML = e
        }, C.prototype.listenTextarea = function () {
            var e = this;
            this.elTextarea.addEventListener("input", (function (t) {
                e.code = t.target.value, e.elCode.innerHTML = _(t.target.value), e.highlight(), setTimeout((function () {
                    e.runUpdate(), e.setLineNumber()
                }), 1)
            })), this.elTextarea.addEventListener("keydown", (function (t) {
                e.handleTabs(t), e.handleSelfClosingCharacters(t), e.handleNewLineIndentation(t)
            })), this.elTextarea.addEventListener("scroll", (function (t) {
                e.elPre.style.transform = "translate3d(-" + t.target.scrollLeft + "px, -" + t.target.scrollTop + "px, 0)", e.elLineNumbers && (e.elLineNumbers.style.transform = "translate3d(0, -" + t.target.scrollTop + "px, 0)")
            }))
        }, C.prototype.handleTabs = function (e) {
            if (this.opts.handleTabs) {
                if (9 !== e.keyCode) return;
                e.preventDefault();
                var t = this.elTextarea, n = t.selectionDirection, a = t.selectionStart, i = t.selectionEnd,
                    s = t.value, r = s.substr(0, a), o = s.substring(a, i), l = s.substring(i),
                    d = " ".repeat(this.opts.tabSize);
                if (a !== i && o.length >= d.length) {
                    var c = a - r.split("\n").pop().length, u = d.length, p = d.length;
                    e.shiftKey ? (s.substr(c, d.length) === d ? (u = -u, c > a ? (o = o.substring(0, c) + o.substring(c + d.length), p = 0) : c === a ? (u = 0, p = 0, o = o.substring(d.length)) : (p = -p, r = r.substring(0, c) + r.substring(c + d.length))) : (u = 0, p = 0), o = o.replace(new RegExp("\n" + d.split("").join("\\"), "g"), "\n")) : (r = r.substr(0, c) + d + r.substring(c, a), o = o.replace(/\n/g, "\n" + d)), t.value = r + o + l, t.selectionStart = a + u, t.selectionEnd = a + o.length + p, t.selectionDirection = n
                } else t.value = r + d + l, t.selectionStart = a + d.length, t.selectionEnd = a + d.length;
                var g = t.value;
                this.updateCode(g), this.elTextarea.selectionEnd = i + this.opts.tabSize
            }
        }, C.prototype.handleSelfClosingCharacters = function (e) {
            if (this.opts.handleSelfClosingCharacters) {
                var t = e.key;
                if (["(", "[", "{", "<", "'", '"'].includes(t) || [")", "]", "}", ">", "'", '"'].includes(t)) switch (t) {
                    case"(":
                    case")":
                    case"[":
                    case"]":
                    case"{":
                    case"}":
                    case"<":
                    case">":
                    case"'":
                    case'"':
                        this.closeCharacter(t)
                }
            }
        }, C.prototype.setLineNumber = function () {
            this.lineNumber = this.code.split("\n").length, this.opts.lineNumbers && this.updateLineNumbersCount()
        }, C.prototype.handleNewLineIndentation = function (e) {
            if (this.opts.handleNewLineIndentation && 13 === e.keyCode) {
                e.preventDefault();
                var t = this.elTextarea, n = t.selectionStart, a = t.selectionEnd, i = t.value, s = i.substr(0, n),
                    r = i.substring(a), o = i.lastIndexOf("\n", n - 1), l = o + i.slice(o + 1).search(/[^ ]|$/),
                    d = l > o ? l - o : 0, c = s + "\n" + " ".repeat(d) + r;
                t.value = c, t.selectionStart = n + d + 1, t.selectionEnd = n + d + 1, this.updateCode(t.value)
            }
        }, C.prototype.closeCharacter = function (e) {
            var t = this.elTextarea.selectionStart, n = this.elTextarea.selectionEnd;
            if (this.skipCloseChar(e)) {
                var a = this.code.substr(n, 1) === e, i = a ? n + 1 : n, s = !a && ["'", '"'].includes(e) ? e : "",
                    r = "" + this.code.substring(0, t) + s + this.code.substring(i);
                this.updateCode(r), this.elTextarea.selectionEnd = ++this.elTextarea.selectionStart
            } else {
                var o = e;
                switch (e) {
                    case"(":
                        o = String.fromCharCode(e.charCodeAt() + 1);
                        break;
                    case"<":
                    case"{":
                    case"[":
                        o = String.fromCharCode(e.charCodeAt() + 2)
                }
                var l = this.code.substring(t, n), d = "" + this.code.substring(0, t) + l + o + this.code.substring(n);
                this.updateCode(d)
            }
            this.elTextarea.selectionEnd = t
        }, C.prototype.skipCloseChar = function (e) {
            var t = this.elTextarea.selectionStart, n = this.elTextarea.selectionEnd, a = Math.abs(n - t) > 0;
            return [")", "}", "]", ">"].includes(e) || ["'", '"'].includes(e) && !a
        }, C.prototype.updateCode = function (e) {
            this.code = e, this.elTextarea.value = e, this.elCode.innerHTML = _(e), this.highlight(), this.setLineNumber(), setTimeout(this.runUpdate.bind(this), 1)
        }, C.prototype.updateLanguage = function (e) {
            var t = this.opts.language;
            this.elCode.classList.remove("language-" + t), this.elCode.classList.add("language-" + e), this.opts.language = e, this.highlight()
        }, C.prototype.addLanguage = function (e, t) {
            F.languages[e] = t
        }, C.prototype.populateDefault = function () {
            this.updateCode(this.code)
        }, C.prototype.highlight = function () {
            F.highlightElement(this.elCode, !1)
        }, C.prototype.onUpdate = function (e) {
            if (e && "[object Function]" !== {}.toString.call(e)) throw Error("CodeFlask expects callback of type Function");
            this.updateCallBack = e
        }, C.prototype.getCode = function () {
            return this.code
        }, C.prototype.runUpdate = function () {
            this.updateCallBack && this.updateCallBack(this.code)
        }, C.prototype.enableReadonlyMode = function () {
            this.elTextarea.setAttribute("readonly", !0)
        }, C.prototype.disableReadonlyMode = function () {
            this.elTextarea.removeAttribute("readonly")
        };
        const L = C;
        var O = n(668), N = n.n(O), P = n(733), I = {};
        I.styleTagTransform = g(), I.setAttributes = d(), I.insert = o().bind(null, "head"), I.domAPI = s(), I.insertStyleElement = u(), t()(P.Z, I), P.Z && P.Z.locals && P.Z.locals;

        class ${static get DEFAULT_PLACEHOLDER(){return"// Hello"}
        static

        get
        enableLineBreaks()
        {
            return !0
        }
        constructor({data: e, config: t, api: n, readOnly: a})
        {
            this.api = n, this.readOnly = a, this._CSS = {
                block: this.api.styles.block,
                wrapper: "ce-EditorJsCodeFlask",
                settingsButton: this.api.styles.settingsButton,
                settingsButtonActive: this.api.styles.settingsButtonActive
            }, this.readOnly || (this.onKeyUp = this.onKeyUp.bind(this)), this._placeholder = t.placeholder ? t.placeholder : $.DEFAULT_PLACEHOLDER, this._preserveBlank = void 0 !== t.preserveBlank && t.preserveBlank, this._element, this.data = {}, this.data.code = void 0 === e.code ? "// Séléctionner le type de langage a droite de cette fenêtre" : e.code, this.data.language = void 0 === e.language ? "plain" : e.language, this.data.showlinenumbers = void 0 === e.showlinenumbers || e.showlinenumbers, this.data.editorInstance = {}
        }
        onKeyUp(e)
        {
            if ("Backspace" !== e.code && "Delete" !== e.code) return;
            const {textContent: t} = this._element;
            "" === t && (this._element.innerHTML = "")
        }
        render()
        {
            this._element = document.createElement("div"), this._element.classList.add("editorjs-codeFlask_Wrapper");
            let e = document.createElement("div");
            e.classList.add("editorjs-codeFlask_Editor");
            let t = document.createElement("div");
            return t.classList.add("editorjs-codeFlask_LangDisplay"), t.innerHTML = this.data.language, this._element.appendChild(e), this._element.appendChild(t), this.data.editorInstance = new L(e, {
                language: this.data.language,
                lineNumbers: this.data.showlinenumbers,
                readonly: this.readOnly
            }), this.data.editorInstance.onUpdate((e => {
                let t = e.split("\n").length;
                this._debounce(this._updateEditorHeight(t))
            })), this.data.editorInstance.addLanguage(this.data.language, k().languages[this.data.language]), this.data.editorInstance.updateCode(this.data.code), this._element
        }
        _updateEditorHeight(e)
        {
            let t = 21 * e + 10;
            t < 60 && (t = 60), this._element.style.height = t + "px"
        }
        _debounce(e, t = 500)
        {
            let n;
            return (...a) => {
                clearTimeout(n), n = setTimeout((() => {
                    e.apply(this, a)
                }), t)
            }
        }
        renderSettings()
        {
            const e = document.createElement("div");
            let t = document.createElement("select");
            t.classList.add("small");
            for (var n = 0; n < Object.keys(k().languages).length; n++) if ("extend" != Object.keys(k().languages)[n] && "insertBefore" != Object.keys(k().languages)[n] && "DFS" != Object.keys(k().languages)[n]) {
                var a = document.createElement("option");
                a.value = Object.keys(k().languages)[n], a.text = Object.keys(k().languages)[n], Object.keys(k().languages)[n] == this.data.language && (a.selected = "selected"), t.appendChild(a)
            }
            return t.addEventListener("change", (e => {
                this._updateLanguage(e.target.value)
            })), e.appendChild(t), new (N())(t, {searchable: !0, placeholder: "Language..."}), e
        }
        _toggleLineNumbers = e => {
            this.data.showlinenumbers = !this.data.showlinenumbers
        };
        _updateLanguage = e => {
            this.data.language = e, this._element.querySelector(".editorjs-codeFlask_LangDisplay").innerHTML = this.data.language, this.data.editorInstance.updateLanguage(this.data.language)
        };
        save(e)
        {
            return {
                code: this.data.editorInstance.getCode(),
                language: this.data.language,
                showlinenumbers: this.data.showlinenumbers
            }
        }
        static
        get
        isReadOnlySupported()
        {
            return !0
        }
        static
        get
        toolbox()
        {
            return {icon: y(), title: "Code"}
        }
    }
})(), a.default
})
()
))
;