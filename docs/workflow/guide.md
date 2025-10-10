# 🧭 **Workflow Guide**

## 🚀 **GitHub Workflow**

### 📌 **Branch Strategy**

Utilizamos uma estratégia simples baseada em **feature branches**:

- `main`: branch estável, sempre pronta para release.
- `feature/*`: usada para desenvolvimento de novas funcionalidades.
- `fix/*`: usada para correções pontuais.
- `docs/*`: usada para alterações apenas na documentação.

---

## Conventional Commits

Para manter um histórico limpo e facilitar geração de changelogs e versionamento semântico, seguimos o padrão de [Conventional Commits](https://www.conventionalcommits.org/).

### Tipos comuns

| Tipo         | Uso                                                            |
| ------------ | -------------------------------------------------------------- |
| **feat**     | Adição de nova funcionalidade                                  |
| **fix**      | Correção de bugs                                               |
| **docs**     | Alterações apenas na documentação                              |
| **style**    | Mudanças que não afetam lógica (formatação, espaçamento, lint) |
| **refactor** | Refatoração de código sem mudar comportamento                  |
| **test**     | Adição ou modificação de testes                                |
| **chore**    | Tarefas de manutenção (build, configs, deps, etc.)             |
| **perf**     | Melhorias de performance                                       |

### Exemplos

```bash
	feat(customer): add create customer action
	fix(dto): correct postal code validation
	docs(workflow): add conventional commits section
	refactor(services): simplify customer update method
	test(actions): add unit test for delete customer

```

## Estrutura

### 🛠 **Development Flow**

```bash
# 1. Crie uma nova branch para a feature
git checkout -b feature/customer-module

# 2. Trabalhe na feature, faça commits frequentes e descritivos
git add .
git commit -m "feat: Add CreateCustomer action"
git commit -m "feat: Add UpdateCustomer action"
git commit -m "feat: Add tests for Customer CRUD"

# 3. Quando estiver pronto, suba a branch e abra um PR
git push origin feature/customer-module

# 4. Faça o merge na main após revisão e aprovação
# 5. Tagueie uma release quando o milestone for concluído
git tag v0.1.0
git push --tags
```

    v0.x.x = Alpha/Beta (breaking changes OK)
    v1.0.0 = First stable release
    v1.1.0 = New features (backward compatible)
    v1.1.1 = Bug fixes
    v2.0.0 = Breaking changes

## 🧱 **Project Organization**

### 📂 **GitHub Issues + Milestones**

A organização do roadmap é feita com **Milestones** (macro objetivos) e **Issues** (tarefas detalhadas).
Cada milestone agrupa um conjunto de entregas relacionadas.

#### 📦 **Milestones**

```
v0.1.0 - Customer Module
v0.2.0 - Payment Module
v0.3.0 - Subscription Module
v1.0.0 - Production Ready
```

#### 📝 **Issues**

**Customer Module**

```
#1 Create Customer
#2 Update Customer

```

## 🧪 **Tests & Documentation**

- Cada nova feature deve vir com **testes unitários** e/ou **integração** correspondentes.
- Atualize os arquivos de documentação (`/docs/patterns` e `/docs/workflow`) assim que um novo padrão for introduzido.
- Documentação de código (`phpdoc`) é revisada antes de mergear PRs grandes.

---

## 🏷 **Releases**

- As releases são marcadas com **tags semânticas** (ex: `v0.1.0`, `v1.0.0`).
- Cada release deve estar associada a um **milestone fechado**.
- Use as tags para gerar changelogs automaticamente.

---
