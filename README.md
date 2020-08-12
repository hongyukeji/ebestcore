# ebestcore

> 核心

<p align="center">
<a href="https://packagist.org/packages/hongyukeji/ebestcore"><img src="https://poser.pugx.org/hongyukeji/ebestcore/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/hongyukeji/ebestcore"><img src="https://poser.pugx.org/hongyukeji/ebestcore/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/hongyukeji/ebestcore"><img src="https://poser.pugx.org/hongyukeji/ebestcore/v/unstable" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/hongyukeji/ebestcore"><img src="https://poser.pugx.org/hongyukeji/ebestcore/license" alt="License"></a>
</p>

发布内核版本
-------------------

```
git tag v3.9.120 && git push origin v3.9.120 master
```

Git
-------------------

> Git 是一个开源的分布式版本控制系统，用于敏捷高效地处理任何或小或大的项目。

> Git 是 Linus Torvalds 为了帮助管理 Linux 内核开发而开发的一个开放源码的版本控制软件。

> Git 与常用的版本控制工具 CVS, Subversion 等不同，它采用了分布式版本库的方式，不必服务器端软件支持。

```

# 上传 修改
git add . && git commit -a -m "Initial commit" && git push origin master

# 拉取 更新并强制覆盖本地文件
$ git fetch --all && git reset --hard origin/master && git pull

# Git版本号
$ git tag
$ git tag v1.0.0
$ git push origin v1.0.0 master

# Git分支
$ git checkout -b dev
$ git push origin dev

# git 删除本地标签
git tag -d v1.0.0  

# git 删除远程标签
git push origin :refs/tags/v1.0.0 
```