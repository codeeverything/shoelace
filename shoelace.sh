#!

git remote add -f shoelace https://github.com/codeeverything/shoelace.git
git merge -s ours --no-commit shoelace/master
git read-tree --prefix=.shoelace -u shoelace/master
git commit -m "Merge Shoelace into .shoelace"
git remote remove shoelace