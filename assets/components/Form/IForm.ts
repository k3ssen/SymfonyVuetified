export default interface IForm {
    rendered: boolean;
    vars: IFormVars;
    children: IForm[];
}

interface IFormVars {
    block_prefixes: string[];
    value: any;
    data: any;
    name: string;
    full_name: string;
    label?: string|boolean;
    btn_add_txt?: string;
    btn_delete_txt?: string;
    required?: boolean;
    compound?: boolean;
    multiple?: boolean;
    allow_add?: boolean;
    allow_delete?: boolean;
    prototype?: any;
    attr?: any;
    row_attr?: any;
    label_attr?: any;
    choices?: object|string[]|number[];
    errors?: any;
    help?: any;
}
