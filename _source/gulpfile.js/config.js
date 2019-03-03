/**
 * config
 * contains preferences for gulp tasks, folders, extensions et al
 */

const config = {
    // CopyModule
    // copy module from source to app
    'copyModule': [
        {
            'title': 'Module 01 Input',
            'sourceFile': '../modules/module_01.input.php',
            'basename': 'input',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/01\ -\ The\ Configurator\ \[1\]'
        },
        {
            'title': 'Module 01 Output',
            'sourceFile': '../modules/module_01.output.php',
            'basename': 'output',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/01\ -\ The\ Configurator\ \[1\]'
        },
        {
            'title': 'Module 02 Input',
            'sourceFile': '../modules/module_02.input.php',
            'basename': 'input',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/02\ -\ The\ Options\ \[2\]'
        },
        {
            'title': 'Module 02 Output',
            'sourceFile': '../modules/module_02.output.php',
            'basename': 'output',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/02\ -\ The\ Options\ \[2\]'
        },
        {
            'title': 'Module 03 Input',
            'sourceFile': '../modules/module_03.input.php',
            'basename': 'input',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/03\ -\ The\ Order\ \[3\]'
        },
        {
            'title': 'Module 03 Output',
            'sourceFile': '../modules/module_03.output.php',
            'basename': 'output',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/03\ -\ The\ Order\ \[3\]'
        },
        {
            'title': 'Module 04 Input',
            'sourceFile': '../modules/module_04.input.php',
            'basename': 'input',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/04\ -\ The\ Overview\ \[4\]'
        },
        {
            'title': 'Module 04 Output',
            'sourceFile': '../modules/module_04.output.php',
            'basename': 'output',
            'destinationFolder': '../../../../../theme/private/redaxo/modules/04\ -\ The\ Overview\ \[4\]'
        },
    ],

    'watchModules': {
        'watchFiles': ['../modules/**/*.php']
    },

    // Scripts
    'scripts': {
        'sourceFiles': ['./assets/scripts/scripts.js'],
        'destinationFolder': '../assets/js',
        'watchFiles': ['./assets/scripts/**/*.js'],
        'cleanFiles': ['../assets/js/*.{js,map}']
    },

    // Watch
    // watches for file changes and fires up related tasks
    'watch': [
        {'watchModules': ['copyModule']},
        {'scripts': ['scripts']}
    ]
};

module.exports = config;