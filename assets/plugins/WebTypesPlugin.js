/**
 * Plugin for webpack that scans 'assets/components' for global components (files that end wit .global.vue).
 * A web-types.json file is created, with references to these global components.
 *
 * It won't give autocompletion for properties, events and slots, but in the very least it will show completion for the
 * name and link to the component/file.
 *
 * See https://github.com/JetBrains/web-types for more info about web-types.
 */

const filesystemWrite = require('fs');

function writeJsonFile (obj, file) {
    const stream = filesystemWrite.createWriteStream(file);
    stream.once('open', () => {
        stream.write(JSON.stringify(obj, null, 2));
        stream.end();
    });
}

class WebTypesPlugin {
    apply(compiler) {
        compiler.hooks.done.tap('MyWebpackPlugin', (
            stats /* stats is passed as an argument when done hook is tapped.  */
        ) => {
            const webTypes = {
                $schema: 'http://json.schemastore.org/web-types.json',
                framework: 'vue',
                name: 'project',
                version: '1',
                contributions: {
                    html: {
                        'types-syntax': 'typescript',
                        tags: [],
                        attributes: [],
                    },
                },
            }
            const glob = require('glob');
            glob.sync( './assets/components/**/*.global.vue' ).forEach( function( file ) {
                const name = file.split('/').pop().replace(/\.global\.\w+$/, '');

                const tag = {
                    name,
                    source: { module: file, symbol: "default" },
                    // TODO: read attributes/events/slots from file
                    // attributes,
                    // events,
                    // slots,
                };
                webTypes.contributions.html.tags.push(tag);
            });

            writeJsonFile(webTypes, 'web-types.json');
        });
    }
}

module.exports = WebTypesPlugin;