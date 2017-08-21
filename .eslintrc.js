// http://eslint.org/docs/user-guide/configuring
module.exports = {
    root: true,
    parser: 'babel-eslint',
    parserOptions: {
        sourceType: 'module'
    },
    env: {
        browser: true,
        es6: true,
        jquery: true
    },
    // https://github.com/feross/standard/blob/master/RULES.md#javascript-standard-style
    extends: 'standard',
    // required to lint *.vue files
    plugins: [
        'html'
    ],
    globals: {
        weMail: true
    },
    // rules are mostly copied from Bootstrap v4-dev
    // https://raw.githubusercontent.com/twbs/bootstrap/v4-dev/js/.eslintrc.json
    rules: {
        // Possible Errors
        "no-await-in-loop": "error",
        "no-compare-neg-zero": "error",
        "no-extra-parens": "error",
        "no-prototype-builtins": "off",
        "no-template-curly-in-string": "error",
        "valid-jsdoc": "error",

        // Best Practices
        "accessor-pairs": "error",
        "array-callback-return": "error",
        "block-scoped-var": "error",
        "class-methods-use-this": "off",
        "complexity": "error",
        "consistent-return": "error",
        "curly": "error",
        "default-case": "error",
        "dot-location": ["error", "property"],
        "dot-notation": "error",
        "eqeqeq": "error",
        "guard-for-in": "error",
        "no-alert": "error",
        "no-caller": "error",
        "no-div-regex": "error",
        "no-else-return": "error",
        "no-empty-function": "error",
        "no-eq-null": "error",
        "no-eval": "error",
        "no-extend-native": "error",
        "no-extra-bind": "error",
        "no-extra-label": "error",
        "no-floating-decimal": "error",
        "no-implicit-coercion": "error",
        "no-implicit-globals": "error",
        "no-implied-eval": "error",
        "no-invalid-this": "off",
        "no-iterator": "error",
        "no-labels": "error",
        "no-lone-blocks": "error",
        "no-loop-func": "error",
        "no-magic-numbers": ["error", {
          "ignore": [-1, 0, 1],
          "ignoreArrayIndexes": true
          }
        ],
        "no-multi-spaces": ["error", {
          "ignoreEOLComments": true,
          "exceptions": {
            "AssignmentExpression": true,
            "ArrowFunctionExpression": true,
            "CallExpression": true,
            "VariableDeclarator": true
            }
          }
        ],
        "no-multi-str": "error",
        "no-new": "error",
        "no-new-func": "error",
        "no-new-wrappers": "error",
        "no-octal-escape": "error",
        "no-param-reassign": "off",
        "no-proto": "error",
        "no-restricted-properties": "error",
        "no-return-assign": "off",
        "no-return-await": "error",
        "no-script-url": "error",
        "no-self-compare": "error",
        "no-sequences": "error",
        "no-throw-literal": "error",
        "no-unmodified-loop-condition": "error",
        "no-unused-expressions": "error",
        "no-useless-call": "error",
        "no-useless-concat": "error",
        "no-useless-escape": "error",
        "no-useless-return": "off",
        "no-void": "error",
        "no-warning-comments": "off",
        "no-with": "error",
        "prefer-promise-reject-errors": "error",
        "radix": "error",
        "require-await": "error",
        "vars-on-top": "error",
        "wrap-iife": "error",
        "yoda": "error",

        // Strict Mode
        "strict": "error",

        // Variables
        "init-declarations": "off",
        "no-catch-shadow": "error",
        "no-label-var": "error",
        "no-restricted-globals": "error",
        "no-shadow": "off",
        "no-shadow-restricted-names": "error",
        "no-undef-init": "error",
        "no-undefined": "off",
        "no-use-before-define": "off",

        // Node.js and CommonJS
        "callback-return": "off",
        "global-require": "error",
        "handle-callback-err": "error",
        "no-mixed-requires": "error",
        "no-new-require": "error",
        "no-path-concat": "error",
        "no-process-env": "off",
        "no-process-exit": "error",
        "no-restricted-modules": "error",
        "no-sync": "error",

        // Stylistic Issues
        "array-bracket-spacing": "error",
        "block-spacing": "error",
        "brace-style": "error",
        "camelcase": "error",
        "capitalized-comments": "off",
        "comma-dangle": "error",
        "comma-spacing": "error",
        "comma-style": "error",
        "computed-property-spacing": "error",
        "consistent-this": "off",
        "eol-last": "error",
        "func-call-spacing": "error",
        "func-name-matching": "error",
        "func-names": "off",
        "func-style": ["error", "declaration"],
        "id-blacklist": "error",
        "id-length": "off",
        "id-match": "error",
        "indent": ["error", 4],
        "indent-legacy": ["error", 4, { "SwitchCase": 1 }],
        "jsx-quotes": "error",
        "key-spacing": "off",
        "keyword-spacing": "error",
        "line-comment-position": "off",
        "linebreak-style": ["error", "unix"],
        "lines-around-comment": "off",
        "lines-around-directive": "error",
        "max-depth": ["error", 10],
        "max-len": "off",
        "max-lines": "off",
        "max-nested-callbacks": "error",
        "max-params": "off",
        "max-statements": "off",
        "max-statements-per-line": "error",
        "multiline-ternary": "off",
        "new-cap": ["error", { "capIsNewExceptionPattern": "$.*" }],
        "new-parens": "error",
        "newline-after-var": "off",
        "newline-before-return": "off",
        "newline-per-chained-call": ["error", { "ignoreChainWithDepth": 5 }],
        "no-array-constructor": "error",
        "no-bitwise": "error",
        "no-continue": "off",
        "no-inline-comments": "off",
        "no-lonely-if": "error",
        "no-mixed-operators": "off",
        "no-multi-assign": "error",
        "no-multiple-empty-lines": "error",
        "no-negated-condition": "off",
        "no-nested-ternary": "error",
        "no-new-object": "error",
        "no-plusplus": "off",
        "no-restricted-syntax": "error",
        "no-tabs": "error",
        "no-ternary": "off",
        "no-trailing-spaces": "error",
        "no-underscore-dangle": "off",
        "no-unneeded-ternary": "error",
        "no-whitespace-before-property": "error",
        "nonblock-statement-body-position": "error",
        "object-curly-newline": ["error", { "minProperties": 1 }],
        "object-curly-spacing": ["error", "always"],
        "object-property-newline": "error",
        "one-var": ["error", "never"],
        "one-var-declaration-per-line": "error",
        "operator-assignment": "error",
        "operator-linebreak": "off",
        "padded-blocks": "off",
        "quote-props": ["error", "as-needed"],
        "quotes": ["error", "single"],
        "require-jsdoc": "off",
        "semi": ["error", "always"],
        "semi-spacing": "error",
        "sort-keys": "off",
        "sort-vars": "error",
        "space-before-blocks": "error",
        "space-before-function-paren": ["error", {
            "anonymous": "always",
            "named": "never"
        }],
        "space-in-parens": "error",
        "space-infix-ops": "error",
        "space-unary-ops": "error",
        "spaced-comment": "error",
        "template-tag-spacing": "error",
        "unicode-bom": "error",
        "wrap-regex": "off",

        // ECMAScript 6
        "arrow-body-style": "off",
        "arrow-parens": "error",
        "arrow-spacing": "error",
        "generator-star-spacing": "error",
        "no-confusing-arrow": "error",
        "no-duplicate-imports": "error",
        "no-restricted-imports": "error",
        "no-useless-computed-key": "error",
        "no-useless-constructor": "error",
        "no-useless-rename": "error",
        "no-var": "error",
        "object-shorthand": "error",
        "prefer-arrow-callback": "error",
        "prefer-const": "error",
        "prefer-destructuring": "off",
        "prefer-numeric-literals": "error",
        "prefer-rest-params": "error",
        "prefer-spread": "error",
        "prefer-template": "error",
        "rest-spread-spacing": "error",
        "sort-imports": "off",
        "symbol-description": "error",
        "template-curly-spacing": "error",
        "yield-star-spacing": "error"
    }
}
