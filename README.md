# Supports des PR de dégradation des couleurs Ansi4 (Symfony Console)

Cette branche orpheline ne contient aucun fichier Symfony. Elle rassemble les supports produits pour deux propositions de modification de la dégradation des couleurs hexadécimales en mode Ansi4 : la PR A (teinte la plus proche) et la PR B′ (rétablissement du contrôle de saturation de #46944), ainsi que leur comparaison.

## Branches et commits

| Proposition | Branche | Commit | État |
|---|---|---|---|
| A | `claude/degrade-hex-color-ansi4-8ja2ir` | `78ec820239` | poussée |
| B′ | `claude/ansi4-saturation-46944-8ja2ir` | `9760bbb104` | NON poussée : appliquer le patch avec `git am` sur l'amont 8.2 `4a6928d61a` |

## Artefacts publiés

- A : https://claude.ai/artifact/Sv1ZvnM9b2E1UVvF44mmYj
- B′ : https://claude.ai/artifact/LL9HM1gU27ZqNbUKWV7Emb
- A contre B′ : https://claude.ai/artifact/N9MZfLwb4yPhtxbBNoZ2Kf

## Ouvrir chaque PR

1. Ouvrir l'URL contenue dans `pr-link.txt` du répertoire concerné dans un navigateur (le titre et le corps sont préremplis).
2. Glisser-déposer les deux images PNG (avant et après) dans les emplacements prévus du corps de la PR.

## pr-a-closest-hue/

- `pr-description.md` : description de la PR A.
- `pr-link.txt` : lien de création de la PR A avec titre et corps préremplis.
- `ansi4-before.png` : image du rendu avant la modification.
- `ansi4-after.png` : image du rendu après la modification.
- `console-ansi4-closest-hue-8.2.patch` : patch de la PR A à appliquer sur la branche 8.2.
- `artifact/ansi4-degradation-16.html` : source de l'artefact interactif de la PR A (grille de 16 niveaux par canal).
- `artifact/gen16.php` : script générant les données de l'artefact.
- `artifact/data16.json` : données de l'artefact.
- `artifact/ansi4-degradation-16-all-views.png` : capture de l'ensemble des vues de l'artefact.
- `generators/gen-pr.php` : script générant les données des images de la PR.
- `generators/data-pr.json` : données utilisées pour les images de la PR.
- `generators/pr_png.py` : script produisant les images PNG avant/après.
- `generators/stats.php` : script de statistiques sur les écarts entre la PR A (teinte la plus proche) et la PR B′ (contrôle de saturation) sur la grille de 16 niveaux par canal ; il classe les couleurs dont le résultat diffère (noir issu d'un gris, noir issu du seuil de saturation, noir autre, blanc, teinte, teinte non voisine). Dépend de `comparison-a-vs-b-prime/gen-diff.php`.

## pr-b-prime-saturation/

- `pr-description.md` : description de la PR B′.
- `pr-link.txt` : lien de création de la PR B′ avec titre et corps préremplis.
- `ansi4-saturation-before.png` : image du rendu avant la modification.
- `ansi4-saturation-after.png` : image du rendu après la modification.
- `console-ansi4-saturation-46944-8.2.patch` : patch de la PR B′ (commit `9760bbb104`).
- `artifact/ansi4-saturation.html` : source de l'artefact interactif de la PR B′.
- `artifact/data.json` : données de l'artefact.
- `generators/gen.php` : script générant les données de l'artefact.
- `generators/gen-pr.php` : script générant les données des images de la PR.
- `generators/data-pr.json` : données utilisées pour les images de la PR.
- `generators/pr_png.py` : script produisant les images PNG avant/après.
- `generators/sat2/*.php` : scripts de la seconde série de vérifications (génération, cas limites, rapports).

## comparison-a-vs-b-prime/

- `ansi4-implementations-diff.html` : page comparant les deux implémentations (A contre B′).
- `data-diff.json` : données de la comparaison.
- `gen-diff.php` : script générant les données de la comparaison.
- `src/build.php` : assemblage de la page à partir du modèle et des données.
- `src/template.html` : modèle de la page.
- `src/shot.js` : script de capture d'écran de la page.

## qa/

- `phpstan-pr.neon` : configuration PHPStan utilisée pour contrôler les PR.
- `psalm-pr-1.xml` : configuration Psalm (niveau 1).
- `psalm-pr-5.xml` : configuration Psalm (niveau 5).
