#!/bin/bash
export PATH=$PATH:/home/codingst/www/git/git/usr/bin/:/home/codingst/modman-relative-links/

for gitdir in `find ../.modman/ -name .git`; 
    do 
        workdir=${gitdir%/*}; 
        echo; 
        echo $workdir; 
        git --git-dir=$gitdir --work-tree=$workdir status; 
    done
