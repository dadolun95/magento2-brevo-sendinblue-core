/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license    This code is licensed under MIT license (see LICENSE for details)
 */

define(
    ["underscore"]
    , function(_) {
        return function(config, element)
        {
            document.addEventListener("sib_initialized", function(e) {
                if (!_.isUndefined(config.pageData) && !_.isEmpty(config.pageData)) {
                    sendinblue.page(config.pageData);
                }
            });
        };
    }
);
