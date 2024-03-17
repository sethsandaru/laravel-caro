interface ImportMetaEnv {
  [key: string]: string;
}

interface ImportMeta {
  url: string;

  readonly env: ImportMetaEnv;
}
