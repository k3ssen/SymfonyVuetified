<template>
  <div class="text-editor-type mb-5">
    <input type="hidden" :name="form.vars.full_name" :value="form.vars.data" />
    <quill-editor
        class="editor"
        ref="myTextEditor"
        v-model="form.vars.data"
        :options="editorOption"
        @blur="onEditorBlur($event)"
        @focus="onEditorFocus($event)"
        @ready="onEditorReady($event)"
    />
    <input ref="fileInput" type="file" @change="uploadFunction" hidden>

    <div class="d-block d-md-none" style="height: 100px;"></div>
    <div class="d-none d-md-block d-lg-none" style="height: 50px;"></div>
  </div>
</template>

<script lang="ts">
import {quillEditor} from 'vue-quill-editor';
import FormWidgetMixin from "@k3ssen/symfony-vuetified/components/Form/FormWidgetMixin";
import './quill-form-type-setup';
import {Component, Mixins} from "vue-property-decorator";

@Component({
    components: { quillEditor }
})
export default class SvTextEditor extends Mixins(FormWidgetMixin) {
    get editor() {
        return (this.$refs.myTextEditor as any).quill;
    }
    get fileInput() {
        return this.$refs.fileInput;
    }
    selectedFile: any = '';
    editorOption: any = {
        modules: {
            toolbar: {
                container: [
                    [
                        'bold', 'italic', 'underline', 'strike',
                        'blockquote', /*'code-block'*/
                        {'header': 1}, {'header': 2},
                        {'list': 'ordered'}, {'list': 'bullet'},
                        {'script': 'sub'}, {'script': 'super'},
                        {'indent': '-1'}, {'indent': '+1'},
                        // {'size': ['small', false, 'large', 'huge']},
                        {'color': []}, {'background': []},
                        {'align': []},
                        'clean',
                        'link', 'image', 'video'
                    ],
                ],
                handlers: {
                    image: this.clickImage,
                },
            },
            imageResize: {
                modules: ["Resize", "DisplaySize", "Toolbar"]
            }
        }
    }
    clickImage() {
        (this.fileInput as any).click();
    }
    async uploadFunction(e: any) {
        this.selectedFile = e.target.files[0];
        var formData = new FormData();
        formData.append("file", this.selectedFile);
        formData.append("name", this.selectedFile.name);
        const response = await fetch('/upload-image', {
            method: 'POST',
            body: formData,
        });
        const status = response.status;
        if (status === 413) {
            alert('This file is too large.');
        } else {
            const reponseJson = await response.json();
            if (reponseJson.url) {
                const range = this.editor.getSelection();
                this.editor.insertEmbed(range.index, 'image', reponseJson.url);
            } else if (reponseJson.error) {
                alert(reponseJson.error);
            } else {
                alert('Something went wrong.');
            }
        }
    }
    onEditorBlur(editor: any) {
        // console.log('editor blur!', editor);
    }
    onEditorFocus(editor: any) {
        // console.log('editor focus!', editor);
    }
    onEditorReady(editor: any) {
        // console.log('editor ready!', editor);
    }
}
</script>

<style lang="scss">
.text-editor-type {
    /*display: flex;*/
    /*flex-direction: column;*/
    .editor {
        iframe {
            margin: auto;
            max-width: 800px;
            width: 100%;
            /*height: 400px;*/
        }

        height: 550px;
        /*overflow: hidden;*/
        padding-bottom: 50px;

        img {
            /*float: left;*/
            /*margin: 5px 10px 5px 0;*/
        }

        .ql-tooltip.ql-editing {
            left: 10px !important;
        }

        .ql-toolbar {
            button {
                background: #eee;
                border: none;
                cursor: pointer;
                display: inline-block;
                float: left;
                height: 35px;
                padding: 3px 5px;
                width: 40px;
                margin: 2px;
                border-radius: 3px;
            }
        }

        .ql-color-picker, .ql-icon-picker {
            margin-right: 15px;

            .ql-picker-label {
                border-radius: 3px;
                background: #eee;
                height: 35px !important;
                padding: 3px 5px !important;
                width: 40px !important;
            }
        }

        .ql-icon-picker {
            .ql-picker-options {
                margin-top: 15px !important;

                span {
                    width: 45px;
                    height: 45px;
                }
            }
        }

        .ql-color-picker {
            .ql-picker-options {
                width: 250px;
                margin-top: 15px !important;

                span {
                    width: 30px;
                    height: 30px;
                }
            }
        }
    }

    .ql-container {
        font-size: 1em;
    }
}
</style>