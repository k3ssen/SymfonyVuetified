export default interface IForm {
    rendered: boolean;
    vars: IFormVars;
    children: object|IForm[];
}

interface IFormVars {
    block_prefixes: string[];
    value: any;
    data: any;
    id: string;
    name: string;
    full_name: string;
    label: string|boolean;
    allow_add?: boolean;
    allow_delete?: boolean;
    btn_add_txt?: string;
    btn_delete_txt?: string;
    required: boolean;
    disabled?: boolean;
    compound: boolean;
    multiple?: boolean;
    prototype?: any;
    attr: any;
    row_attr: any;
    label_attr?: any;
    choices?: object|string[]|number[];
    errors?: string;
    help?: any;
    action?: string;
    method?: string;
    // add more attributes when needed.
}
