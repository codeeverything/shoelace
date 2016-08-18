git remote add -f shoelace https://github.com/codeeverything/shoelace.git
git merge -s ours --no-commit shoelace/master
git read-tree --prefix=SUBDIR -u shoelace/master
git commit -m "Merge ARGS Shoelace modules to SUBDIR"
#git pull -s subtree shoelace master  # updates things checkout from shoelace - we don't really need this
#git remote remove shoelace?
