
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python 入门指南</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #3776ab;
            --secondary-color: #ffd343;
            --accent-color: #306998;
            --text-color: #2c3e50;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --python-gradient: linear-gradient(135deg, #3776ab 0%, #ffd343 100%);
        }

        body {
            font-family: 'Noto Serif SC', serif;
            line-height: 1.7;
            color: var(--text-color);
            background: var(--bg-color);
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }

        /* 头部样式 */
        .header {
            background: var(--python-gradient);
            color: white;
            padding: 60px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="python-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23python-pattern)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .header h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideInDown 1s ease-out;
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header .subtitle {
            font-size: 1.3em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            animation: slideInUp 1s ease-out 0.3s both;
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 导航栏 */
        .nav {
            background: var(--card-bg);
            padding: 20px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .nav-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            margin: 5px;
        }

        .nav-link {
            display: block;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            background: #f8fafc;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .nav-link:hover::before {
            left: 0;
        }

        .nav-link:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* 内容区域 */
        .content {
            padding: 40px 0;
        }

        .section {
            background: var(--card-bg);
            margin-bottom: 30px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient);
        }

        .section h2 {
            font-size: 2.2em;
            margin-bottom: 25px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .section h3 {
            font-size: 1.6em;
            margin: 30px 0 20px 0;
            color: var(--accent-color);
            position: relative;
            padding-left: 20px;
        }

        .section h3::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: var(--secondary-color);
            border-radius: 50%;
        }

        .section p {
            margin: 20px 0;
            font-size: 1.1em;
            line-height: 1.8;
        }

        /* 列表样式 */
        .section ul, .section ol {
            margin: 20px 0;
            padding-left: 0;
        }

        .section li {
            margin: 12px 0;
            padding: 12px 20px;
            background: rgba(103, 126, 234, 0.05);
            border-left: 4px solid var(--primary-color);
            border-radius: 8px;
            list-style: none;
            position: relative;
            transition: all 0.3s ease;
        }

        .section li:hover {
            background: rgba(103, 126, 234, 0.1);
            transform: translateX(5px);
        }

        .section ol li {
            counter-increment: item;
            padding-left: 50px;
        }

        .section ol li::before {
            content: counter(item);
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gradient);
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9em;
        }

        .section ol {
            counter-reset: item;
        }

        /* 链接样式 */
        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
        }

        a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: width 0.3s ease;
        }

        a:hover::after {
            width: 100%;
        }

        a:hover {
            color: var(--accent-color);
        }

        /* 代码样式 */
        code {
            font-family: 'JetBrains Mono', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.9em;
            color: #e91e63;
            font-weight: 600;
            word-break: break-word;
        }

        /* 表格样式 */
        .table-container {
            overflow-x: auto;
            margin: 30px 0;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        th, td {
            padding: 18px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: var(--gradient);
            color: white;
            font-weight: 600;
            position: relative;
        }

        tr:hover {
            background: rgba(103, 126, 234, 0.05);
        }

        /* 特殊卡片 */
        .highlight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }

        .highlight-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .highlight-card h3 {
            color: white;
            margin-bottom: 15px;
        }

        .highlight-card h3::before {
            background: var(--secondary-color);
        }

        /* 页脚 */
        .footer {
            background: var(--text-color);
            color: white;
            text-align: center;
            padding: 40px 0;
            margin-top: 50px;
        }

        .footer p {
            opacity: 0.8;
        }

        /* 响应式设计 */
        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header {
                padding: 40px 0;
            }

            .header h1 {
                font-size: 2.2em;
                margin-bottom: 15px;
            }

            .header .subtitle {
                font-size: 1.1em;
            }

            .nav {
                padding: 15px 0;
            }

            .nav-container {
                padding: 0 15px;
            }

            .nav-menu {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }

            .nav-item {
                margin: 2px 0;
            }

            .nav-link {
                padding: 12px 25px;
                text-align: center;
                border-radius: 8px;
                min-width: 120px;
            }

            .section {
                padding: 20px;
                margin-bottom: 20px;
            }

            .section h2 {
                font-size: 1.6em;
                margin-bottom: 20px;
            }

            .section h3 {
                font-size: 1.3em;
                margin: 25px 0 15px 0;
            }

            .section p {
                font-size: 1em;
                line-height: 1.6;
            }

            .section li {
                padding: 10px 15px;
                margin: 10px 0;
                font-size: 0.95em;
            }

            .section ol li {
                padding-left: 45px;
            }

            .section ol li::before {
                width: 22px;
                height: 22px;
                font-size: 0.8em;
                left: 12px;
            }

            .highlight-card {
                padding: 20px;
                margin: 20px 0;
            }

            .table-container {
                font-size: 0.9em;
            }

            th, td {
                padding: 12px 8px;
                font-size: 0.85em;
            }

            .footer {
                padding: 30px 0;
                font-size: 0.9em;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 5px;
            }

            .header {
                padding: 30px 0;
            }

            .header h1 {
                font-size: 1.8em;
                margin-bottom: 10px;
            }

            .header .subtitle {
                font-size: 1em;
                padding: 0 10px;
            }

            .nav {
                padding: 10px 0;
            }

            .nav-container {
                padding: 0 10px;
            }

            .nav-menu {
                gap: 8px;
            }

            .nav-item {
                margin: 1px 0;
            }

            .nav-link {
                padding: 10px 20px;
                font-size: 0.9em;
                min-width: 100px;
            }

            .section {
                padding: 15px;
                margin-bottom: 15px;
            }

            .section h2 {
                font-size: 1.4em;
                margin-bottom: 15px;
            }

            .section h3 {
                font-size: 1.2em;
                margin: 20px 0 12px 0;
            }

            .section p {
                font-size: 0.95em;
                line-height: 1.5;
                margin: 15px 0;
            }

            .section li {
                padding: 8px 12px;
                margin: 8px 0;
                font-size: 0.9em;
                border-left-width: 3px;
            }

            .section ol li {
                padding-left: 40px;
            }

            .section ol li::before {
                width: 20px;
                height: 20px;
                font-size: 0.75em;
                left: 10px;
            }

            .highlight-card {
                padding: 15px;
                margin: 15px 0;
            }

            .table-container {
                font-size: 0.8em;
                margin: 20px -15px;
            }

            table {
                border-radius: 0;
            }

            th, td {
                padding: 8px 6px;
                font-size: 0.8em;
            }

            th {
                font-size: 0.75em;
            }

            code {
                font-size: 0.8em;
                padding: 2px 6px;
                word-break: break-all;
            }

            .footer {
                padding: 20px 0;
                font-size: 0.85em;
            }
        }

        /* 超小屏幕优化 */
        @media (max-width: 320px) {
            .header h1 {
                font-size: 1.5em;
            }

            .header .subtitle {
                font-size: 0.9em;
            }

            .section h2 {
                font-size: 1.3em;
            }

            .section h3 {
                font-size: 1.1em;
            }

            .nav-link {
                padding: 8px 16px;
                font-size: 0.85em;
                min-width: 90px;
            }

            .section {
                padding: 12px;
            }

            .section p, .section li {
                font-size: 0.85em;
            }

            .table-container {
                font-size: 0.7em;
            }

            th, td {
                padding: 6px 4px;
                font-size: 0.75em;
            }
        }

        /* 滚动条样式 */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* 按钮样式 */
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: var(--gradient);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>🐍 Python 入门指南</h1>
            <p class="subtitle">开启你的编程之旅，从零基础到Python高手</p>
        </div>
    </div>

    <nav class="nav">
        <div class="nav-container">
            <ul class="nav-menu">
                <li class="nav-item"><a href="#what-is-python" class="nav-link">什么是Python</a></li>
                <li class="nav-item"><a href="#what-can-do" class="nav-link">能做什么</a></li>
                <li class="nav-item"><a href="#how-to-learn" class="nav-link">如何学习</a></li>
                <li class="nav-item"><a href="#resources" class="nav-link">学习资源</a></li>
                <li class="nav-item"><a href="#suggestions" class="nav-link">学习建议</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <div class="section" id="what-is-python">
                <h2>🤔 Python 是什么？</h2>
                <p>Python 是一种高级、解释型、通用的编程语言，由 Guido van Rossum 创建，1991 年首次发布。它的语法简洁、易读，接近自然语言，适合初学者。Python 支持多种编程范式（面向对象、函数式、过程式），因其简单易学和强大功能广受欢迎。</p>
                <ul>
                    <li><strong>💡 简单易学</strong>：语法清晰，适合零基础学习。</li>
                    <li><strong>🌐 跨平台</strong>：支持 Windows、MacOS、Linux 等系统。</li>
                    <li><strong>🆓 开源免费</strong>：社区活跃，资源丰富。</li>
                    <li><strong>🚀 广泛应用</strong>：涵盖 Web 开发、数据科学、人工智能等领域。</li>
                </ul>
            </div>

            <div class="section" id="what-can-do">
                <h2>🛠️ Python 能做什么？</h2>
                <p>Python 用途广泛，适合多种场景，尤其对初学者友好。以下是主要应用领域：</p>
                <ol>
                    <li><strong>🌍 Web 开发</strong>：使用 Django、Flask 框架开发网站，如知乎、豆瓣的部分功能。</li>
                    <li><strong>📊 数据科学与分析</strong>：用 Pandas、NumPy 处理数据，Matplotlib 绘制图表。</li>
                    <li><strong>🤖 人工智能与机器学习</strong>：用 TensorFlow、PyTorch 构建 AI 模型，应用包括图像识别、语音处理。</li>
                    <li><strong>⚙️ 自动化脚本</strong>：编写脚本实现文件处理、网页爬取等自动化任务。</li>
                    <li><strong>🎮 游戏开发</strong>：用 Pygame 开发简单游戏。</li>
                    <li><strong>🔧 其他</strong>：网络安全、科学计算、嵌入式开发（如树莓派项目）。</li>
                </ol>
            </div>

            <div class="section" id="how-to-learn">
                <h2>📚 如何学习 Python？</h2>
                <p>作为大一学生，学习 Python 可以循序渐进。以下是学习路线和建议：</p>

                <h3>🔧 1. 搭建开发环境</h3>
                <ul>
                    <li><strong>安装 Python</strong>：下载最新 3.x 版本（<a href="https://www.python.org">官网</a>）或使用提供的资源：<a href="https://pan.baidu.com/share/init?surl=2-tPpoRCRAhKcYK9m2fX9Q&pwd=twy3">百度网盘</a>（密码：twy3）。</li>
                    <li><strong>编辑器/IDE</strong>：
                        <ul>
                            <li>初学者：IDLE（自带）、Thonny。</li>
                            <li>进阶：VS Code、PyCharm（社区版免费）。</li>
                        </ul>
                    </li>
                    <li><strong>验证</strong>：运行 <code>python --version</code> 检查安装。</li>
                </ul>

                <h3>📖 2. 学习基础知识</h3>
                <ul>
                    <li><strong>核心内容</strong>：变量、数据类型（整数、字符串、列表、字典）、控制结构（if、for、while）、函数、模块。</li>
                    <li><strong>资源推荐</strong>：
                        <ul>
                            <li>文档：<a href="https://www.runoob.com/python3/python3-tutorial.html">菜鸟教程</a>，简洁易懂。</li>
                            <li>视频：<a href="https://space.bilibili.com/1177252794/lists/1222205?type=season">Bilibili 入门视频</a>，生动直观。</li>
                            <li>书籍：《Python 编程：从入门到实践》（Eric Matthes），适合初学者。</li>
                        </ul>
                    </li>
                </ul>

                <h3>💻 3. 实践与项目</h3>
                <ul>
                    <li><strong>小练习</strong>：写计算器、猜数字游戏、处理 Excel 文件。</li>
                    <li><strong>项目</strong>：
                        <ul>
                            <li>初级：个人记账工具。</li>
                            <li>中级：爬取天气数据并可视化。</li>
                            <li>高级：用 Flask 搭建博客。</li>
                        </ul>
                    </li>
                    <li><strong>平台</strong>：<a href="https://leetcode.com">LeetCode</a>、Kaggle 练习算法和数据项目。</li>
                </ul>

                <h3>👥 4. 加入社区</h3>
                <ul>
                    <li><strong>国内</strong>：CSDN、知乎，搜索问题或讨论。</li>
                    <li><strong>国际</strong>：<a href="https://stackoverflow.com">Stack Overflow</a>、Reddit（r/learnpython）。</li>
                    <li><strong>开源</strong>：参与 GitHub 项目，积累经验。</li>
                </ul>

                <div class="highlight-card">
                    <h3>💡 5. 学习建议</h3>
                    <ul>
                        <li>每天 30 分钟练习代码，保持连续性。</li>
                        <li>记录笔记，整理常见错误。</li>
                        <li>循序渐进，先基础后高级库（如 Pandas）。</li>
                        <li>选择感兴趣的项目（如爬虫、游戏）提升动力。</li>
                    </ul>
                </div>
            </div>

            <div class="section" id="resources">
                <h2>📋 学习资源汇总</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>📚 类型</th>
                            <th>📖 资源名称</th>
                            <th>🔗 链接/描述</th>
                        </tr>
                        <tr>
                            <td>文档</td>
                            <td>菜鸟教程 Python 入门</td>
                            <td><a href="https://www.runoob.com/python3/python3-tutorial.html">在线学习</a></td>
                        </tr>
                        <tr>
                            <td>视频</td>
                            <td>Bilibili Python 入门视频</td>
                            <td><a href="https://space.bilibili.com/1177252794/lists/1222205?type=season">观看视频</a></td>
                        </tr>
                        <tr>
                            <td>环境安装</td>
                            <td>Python 环境软件</td>
                            <td><a href="https://pan.baidu.com/share/init?surl=2-tPpoRCRAhKcYK9m2fX9Q&pwd=twy3">百度网盘</a>（密码：twy3）</td>
                        </tr>
                        <tr>
                            <td>书籍</td>
                            <td>《Python 编程：从入门到实践》</td>
                            <td>经典教材，可在图书馆或网上购买。</td>
                        </tr>
                        <tr>
                            <td>在线平台</td>
                            <td>LeetCode</td>
                            <td><a href="https://leetcode.com">练习算法题</a></td>
                        </tr>
                        <tr>
                            <td>社区</td>
                            <td>Stack Overflow</td>
                            <td><a href="https://stackoverflow.com">解决代码问题</a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section" id="suggestions">
                <h2>🎯 给大一学生的建议</h2>
                <p>大一时间充裕，制定小目标（如第一周学变量，第二周写程序），加入编程社团或学习小组，善用搜索引擎和社区，遇到问题别怕，90% 都有答案！</p>
                <div class="highlight-card">
                    <h3>🌟 祝你 Python 学习愉快，未来成为编程大牛！</h3>
                    <p>记住：编程是一门实践性很强的技能，多动手、多思考、多交流，你一定能够掌握这门强大的语言！</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2024 Python 入门指南 | 让编程学习更简单 🐍</p>
        </div>
    </div>

    <script>
        // 平滑滚动到锚点
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // 添加滚动动画
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(section);
        });
    </script>
</body>
</html>

